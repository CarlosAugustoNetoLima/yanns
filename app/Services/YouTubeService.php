<?php

namespace App\Services;

/**
 * Service para buscar vídeos do YouTube Data API v3.
 *
 * Usa sistema de cache em arquivo JSON para minimizar chamadas à API.
 * Quando a API não está configurada ou falha, retorna dados hardcoded
 * como fallback para manter o site funcional.
 */
class YouTubeService
{
    private string $apiKey;
    private string $channelId;
    private int    $cacheTtl;
    private int    $maxResults;
    private string $cacheDir;

    private const API_BASE = 'https://www.googleapis.com/youtube/v3';

    public function __construct()
    {
        $config = require __DIR__ . '/../Config/youtube.php';

        $this->apiKey     = $config['api_key'] ?? '';
        $this->channelId  = $config['channel_id'] ?? '';
        $this->cacheTtl   = $config['cache_ttl'] ?? 21600;
        $this->maxResults = $config['max_results'] ?? 8;

        // Em ambiente serverless (Vercel) o código é read-only; usamos /tmp.
        $cacheDir = __DIR__ . '/../../app/Cache/';
        if (getenv('VERCEL') || !is_dir($cacheDir) || !is_writable($cacheDir)) {
            $cacheDir = rtrim(sys_get_temp_dir(), '/') . '/yanns_youtube_cache/';
        }
        $this->cacheDir = $cacheDir;
        if (!is_dir($this->cacheDir)) {
            @mkdir($this->cacheDir, 0755, true);
        }
    }

    /**
     * Verifica se a API está configurada (tem api_key).
     */
    public function isConfigured(): bool
    {
        return $this->apiKey !== '';
    }

    /**
     * Busca os vídeos mais recentes do canal.
     *
     * @param int $max Quantidade de vídeos a retornar
     * @return array Lista de vídeos no formato padrão
     */
    public function getRecentVideos(int $max = 4): array
    {
        if (!$this->isConfigured()) {
            return self::fallbackRecents();
        }

        $cacheKey = "youtube_recent_{$max}";
        $cached = $this->getCache($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        $videos = $this->searchVideos('date', $max);
        if (empty($videos)) {
            // Tenta usar cache expirado como fallback
            $stale = $this->getStaleCache($cacheKey);
            return $stale ?? self::fallbackRecents();
        }

        $this->setCache($cacheKey, $videos);
        return $videos;
    }

    /**
     * Busca os vídeos mais vistos do canal.
     *
     * @param int $max Quantidade de vídeos a retornar
     * @return array Lista de vídeos no formato padrão
     */
    public function getMostViewedVideos(int $max = 4): array
    {
        if (!$this->isConfigured()) {
            return self::fallbackMostViewed();
        }

        $cacheKey = "youtube_popular_{$max}";
        $cached = $this->getCache($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        $videos = $this->searchVideos('viewCount', $max);
        if (empty($videos)) {
            $stale = $this->getStaleCache($cacheKey);
            return $stale ?? self::fallbackMostViewed();
        }

        $this->setCache($cacheKey, $videos);
        return $videos;
    }

    /**
     * Busca todos os clipes oficiais do canal.
     *
     * @param int $max Quantidade de vídeos a retornar
     * @return array Lista de clipes oficiais
     */
    public function getAllClips(int $max = 8): array
    {
        if (!$this->isConfigured()) {
            return self::fallbackClips();
        }

        $cacheKey = "youtube_clips_{$max}";
        $cached = $this->getCache($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        // Busca vídeos com "clip officiel" no título, ordenados por views
        $videos = $this->searchVideos('viewCount', $max, 'clip officiel');
        if (empty($videos)) {
            $stale = $this->getStaleCache($cacheKey);
            return $stale ?? self::fallbackClips();
        }

        $this->setCache($cacheKey, $videos);
        return $videos;
    }

    /**
     * Retorna o clipe oficial mais recente (para featured/destaque).
     *
     * @return array|null Dados do vídeo ou null
     */
    public function getLatestClip(): ?array
    {
        if (!$this->isConfigured()) {
            return self::fallbackFeatured();
        }

        $cacheKey = 'youtube_latest_clip';
        $cached = $this->getCache($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        // Busca o clipe oficial mais recente por data
        $videos = $this->searchVideos('date', 1, 'clip officiel');
        if (empty($videos)) {
            $stale = $this->getStaleCache($cacheKey);
            return $stale ?? self::fallbackFeatured();
        }

        $video = $videos[0];
        $this->setCache($cacheKey, $video);
        return $video;
    }

    // ─── API Calls ────────────────────────────────────────────────────

    /**
     * Pesquisa vídeos no canal e enriquece com estatísticas.
     *
     * @param string      $order Ordem: 'date', 'viewCount', 'relevance'
     * @param int         $max   Quantidade máxima de resultados
     * @param string|null $query Filtro de texto (ex: "clip officiel")
     * @return array Lista de vídeos formatados
     */
    private function searchVideos(string $order, int $max, ?string $query = null): array
    {
        $params = [
            'part'       => 'snippet',
            'channelId'  => $this->channelId,
            'type'       => 'video',
            'order'      => $order,
            'maxResults' => $max,
            'key'        => $this->apiKey,
        ];
        if ($query !== null) {
            $params['q'] = $query;
        }

        $url = self::API_BASE . '/search?' . http_build_query($params);
        $response = $this->curlGet($url);
        if (!$response) {
            return [];
        }

        $data = json_decode($response, true);
        if (!isset($data['items']) || empty($data['items'])) {
            return [];
        }

        // Coleta os IDs dos vídeos para buscar estatísticas
        $videoIds = [];
        foreach ($data['items'] as $item) {
            $videoIds[] = $item['id']['videoId'] ?? '';
        }
        $videoIds = array_filter($videoIds);

        if (empty($videoIds)) {
            return [];
        }

        // Busca estatísticas (viewCount) e contentDetails (duration)
        $stats = $this->getVideoStatistics($videoIds);

        // Monta array no formato esperado pelos componentes
        $videos = [];
        foreach ($data['items'] as $item) {
            $videoId = $item['id']['videoId'] ?? '';
            if (!$videoId) {
                continue;
            }

            $snippet    = $item['snippet'];
            $viewCount  = (int) ($stats[$videoId]['viewCount'] ?? 0);
            $publishedAt = $snippet['publishedAt'] ?? '';

            // Calcula "tempo atrás" a partir da data de publicação
            $timeAgo = $this->calculateTimeAgo($publishedAt);

            $videos[] = [
                'videoId'     => $videoId,
                'title'       => $snippet['title'] ?? '',
                'views_count' => $viewCount,
                'time_value'  => $timeAgo['value'],
                'time_unit'   => $timeAgo['unit'],
                'imageUrl'    => $this->getBestThumbnail($snippet['thumbnails'] ?? []),
            ];
        }

        return $videos;
    }

    /**
     * Busca estatísticas (views, likes) para uma lista de IDs de vídeo.
     *
     * @param array $videoIds Lista de IDs
     * @return array Mapa [videoId => ['viewCount' => int, ...]]
     */
    private function getVideoStatistics(array $videoIds): array
    {
        $params = [
            'part' => 'statistics',
            'id'   => implode(',', $videoIds),
            'key'  => $this->apiKey,
        ];

        $url = self::API_BASE . '/videos?' . http_build_query($params);
        $response = $this->curlGet($url);
        if (!$response) {
            return [];
        }

        $data = json_decode($response, true);
        $map = [];
        foreach (($data['items'] ?? []) as $item) {
            $map[$item['id']] = $item['statistics'] ?? [];
        }
        return $map;
    }

    // ─── Helpers ──────────────────────────────────────────────────────

    /**
     * Calcula tempo relativo a partir de uma data ISO 8601.
     *
     * @param string $isoDate Data no formato ISO 8601
     * @return array ['value' => int, 'unit' => 'day'|'month'|'year']
     */
    private function calculateTimeAgo(string $isoDate): array
    {
        if (empty($isoDate)) {
            return ['value' => 0, 'unit' => 'day'];
        }

        try {
            $published = new \DateTime($isoDate);
            $now = new \DateTime();
            $diff = $now->diff($published);

            if ($diff->y > 0) {
                return ['value' => $diff->y, 'unit' => 'year'];
            }
            if ($diff->m > 0) {
                return ['value' => $diff->m, 'unit' => 'month'];
            }
            return ['value' => max(1, $diff->d), 'unit' => 'day'];
        } catch (\Exception $e) {
            return ['value' => 0, 'unit' => 'day'];
        }
    }

    /**
     * Escolhe a melhor thumbnail disponível.
     */
    private function getBestThumbnail(array $thumbnails): string
    {
        // Prioridade: maxres > standard > high > medium > default
        foreach (['maxres', 'standard', 'high', 'medium', 'default'] as $size) {
            if (isset($thumbnails[$size]['url'])) {
                return $thumbnails[$size]['url'];
            }
        }
        return '';
    }

    // ─── HTTP ─────────────────────────────────────────────────────────

    private function curlGet(string $url): ?string
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_USERAGENT      => 'Mozilla/5.0 (compatible; Yanns/1.0)',
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($result === false || $result === '' || $httpCode !== 200) {
            return null;
        }
        return $result;
    }

    // ─── Cache ────────────────────────────────────────────────────────

    private function getCacheFile(string $key): string
    {
        return $this->cacheDir . md5($key) . '.json';
    }

    /**
     * Retorna dados do cache se ainda válido (dentro do TTL).
     */
    private function getCache(string $key): mixed
    {
        $file = $this->getCacheFile($key);
        if (!file_exists($file)) {
            return null;
        }
        if (time() - filemtime($file) > $this->cacheTtl) {
            return null; // Expirado — mas NÃO deleta, pra servir como stale fallback
        }
        $data = json_decode(file_get_contents($file), true);
        return $data;
    }

    /**
     * Retorna dados do cache mesmo se expirado (fallback de emergência).
     */
    private function getStaleCache(string $key): mixed
    {
        $file = $this->getCacheFile($key);
        if (!file_exists($file)) {
            return null;
        }
        $data = json_decode(file_get_contents($file), true);
        return $data;
    }

    /**
     * Salva dados no cache.
     */
    private function setCache(string $key, mixed $data): void
    {
        $file = $this->getCacheFile($key);
        file_put_contents($file, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    // ─── Fallback Data ────────────────────────────────────────────────
    // Dados hardcoded originais — usados quando a API não está configurada
    // ou quando todas as tentativas de busca falham.

    private static function fallbackRecents(): array
    {
        return [
            [
                'videoId'     => 'Uy-L1wnHy7U',
                'title'       => 'OLYMPIA 2026',
                'views_count' => 6_200,
                'time_value'  => 11,
                'time_unit'   => 'day',
                'imageUrl'    => 'https://img.youtube.com/vi/Uy-L1wnHy7U/maxresdefault.jpg',
            ],
            [
                'videoId'     => '8fxLKm-mgb4',
                'title'       => 'YANNS - MYTHO (Clip officiel)',
                'views_count' => 578_000,
                'time_value'  => 1,
                'time_unit'   => 'month',
                'imageUrl'    => 'https://img.youtube.com/vi/8fxLKm-mgb4/sddefault.jpg',
            ],
            [
                'videoId'     => 'mwoHkRVOu7g',
                'title'       => 'YANNS - TOKÉ TOKÉ (Clip officiel)',
                'views_count' => 1_600_000,
                'time_value'  => 2,
                'time_unit'   => 'month',
                'imageUrl'    => 'https://img.youtube.com/vi/mwoHkRVOu7g/maxresdefault.jpg',
            ],
            [
                'videoId'     => 'NVPeityE_bY',
                'title'       => 'YANNS - TOKÉ TOKÉ (Lyrics Vidéo)',
                'views_count' => 187_000,
                'time_value'  => 2,
                'time_unit'   => 'month',
                'imageUrl'    => 'https://img.youtube.com/vi/NVPeityE_bY/maxresdefault.jpg',
            ],
        ];
    }

    private static function fallbackMostViewed(): array
    {
        return [
            [
                'videoId'     => 'MmQa-lLy38o',
                'title'       => 'Yanns - Clic clic pan pan (Clip Officiel)',
                'views_count' => 176_365_394,
                'time_value'  => 4,
                'time_unit'   => 'year',
                'imageUrl'    => 'https://img.youtube.com/vi/MmQa-lLy38o/maxresdefault.jpg',
            ],
            [
                'videoId'     => 'OUhlSwULEBE',
                'title'       => "Yanns - Mains en L'air (Clip Officiel)",
                'views_count' => 32_458_131,
                'time_value'  => 3,
                'time_unit'   => 'year',
                'imageUrl'    => 'https://img.youtube.com/vi/OUhlSwULEBE/maxresdefault.jpg',
            ],
            [
                'videoId'     => 'LzEwOWyvW68',
                'title'       => 'Yanns - En détresse (Clip Officiel)',
                'views_count' => 29_757_941,
                'time_value'  => 4,
                'time_unit'   => 'year',
                'imageUrl'    => 'https://img.youtube.com/vi/LzEwOWyvW68/maxresdefault.jpg',
            ],
            [
                'videoId'     => 'E6rYfBcVxUw',
                'title'       => 'Yanns - Soleil et nanas (Clip Officiel)',
                'views_count' => 26_385_648,
                'time_value'  => 3,
                'time_unit'   => 'year',
                'imageUrl'    => 'https://img.youtube.com/vi/E6rYfBcVxUw/maxresdefault.jpg',
            ],
        ];
    }

    private static function fallbackClips(): array
    {
        return [
            ['videoId' => 'MmQa-lLy38o', 'title' => 'Yanns - Clic clic pan pan (Clip Officiel)',  'views_count' => 176_365_394, 'time_value' => 4, 'time_unit' => 'year',  'imageUrl' => 'https://img.youtube.com/vi/MmQa-lLy38o/maxresdefault.jpg'],
            ['videoId' => 'OUhlSwULEBE', 'title' => "Yanns - Mains en L'air (Clip Officiel)",     'views_count' => 32_458_131,  'time_value' => 3, 'time_unit' => 'year',  'imageUrl' => 'https://img.youtube.com/vi/OUhlSwULEBE/maxresdefault.jpg'],
            ['videoId' => 'LzEwOWyvW68', 'title' => 'Yanns - En détresse (Clip Officiel)',        'views_count' => 29_757_941,  'time_value' => 4, 'time_unit' => 'year',  'imageUrl' => 'https://img.youtube.com/vi/LzEwOWyvW68/maxresdefault.jpg'],
            ['videoId' => 'E6rYfBcVxUw', 'title' => 'Yanns - Soleil et nanas (Clip Officiel)',    'views_count' => 26_385_648,  'time_value' => 3, 'time_unit' => 'year',  'imageUrl' => 'https://img.youtube.com/vi/E6rYfBcVxUw/maxresdefault.jpg'],
            ['videoId' => 'WRRVi5aNzQk', 'title' => "YANNS - JE T'AIME (clip officiel)",          'views_count' => 5_030_466,   'time_value' => 2, 'time_unit' => 'year',  'imageUrl' => 'https://img.youtube.com/vi/WRRVi5aNzQk/maxresdefault.jpg'],
            ['videoId' => 'mwoHkRVOu7g', 'title' => 'YANNS - TOKÉ TOKÉ (Clip officiel)',          'views_count' => 1_667_370,   'time_value' => 2, 'time_unit' => 'month', 'imageUrl' => 'https://img.youtube.com/vi/mwoHkRVOu7g/maxresdefault.jpg'],
            ['videoId' => '988rwXMSPic', 'title' => 'YANNS - COCO CHOCO (Clip officiel)',         'views_count' => 986_394,     'time_value' => 5, 'time_unit' => 'month', 'imageUrl' => 'https://img.youtube.com/vi/988rwXMSPic/maxresdefault.jpg'],
            ['videoId' => '8fxLKm-mgb4', 'title' => 'YANNS - MYTHO (Clip officiel)',              'views_count' => 578_021,     'time_value' => 1, 'time_unit' => 'month', 'imageUrl' => 'https://img.youtube.com/vi/8fxLKm-mgb4/sddefault.jpg'],
        ];
    }

    private static function fallbackFeatured(): array
    {
        return [
            'videoId'     => '8fxLKm-mgb4',
            'title'       => 'YANNS - MYTHO (Clip officiel)',
            'views_count' => 578_000,
            'time_value'  => 1,
            'time_unit'   => 'month',
            'imageUrl'    => 'https://img.youtube.com/vi/8fxLKm-mgb4/sddefault.jpg',
        ];
    }
}
