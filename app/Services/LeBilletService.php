<?php

namespace App\Services;

class LeBilletService
{
    private string $checkoutBase = 'https://checkout.lebillet.eu/';
    private int    $cacheTtl     = 300;
    private string $cacheDir;
    private array  $productConfig;

    public function __construct()
    {
        $this->cacheDir = sys_get_temp_dir() . '/lebillet_cache_yanns/';
        if (!is_dir($this->cacheDir)) {
            @mkdir($this->cacheDir, 0755, true);
        }

        $configFile = __DIR__ . '/../Config/products.php';
        $this->productConfig = file_exists($configFile) ? require $configFile : [];
    }

    public function getProducts(int $eventId): array
    {
        $cacheKey = "products_{$eventId}";
        $cached   = $this->getCache($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        $sid = $this->fetchSid($eventId);
        if (!$sid) {
            return [];
        }

        $html = $this->fetchProductsHtml($eventId, $sid);
        if (!$html) {
            return [];
        }

        $products = $this->parseProducts($html);

        usort($products, function (array $a, array $b): int {
            $orderA = $this->productConfig[$a['id']]['sort_order'] ?? 999;
            $orderB = $this->productConfig[$b['id']]['sort_order'] ?? 999;
            return $orderA <=> $orderB;
        });

        $this->setCache($cacheKey, $products);
        return $products;
    }

    public function getProduct(int $eventId, string $productId): ?array
    {
        foreach ($this->getProducts($eventId) as $p) {
            if ($p['id'] === $productId) {
                return $p;
            }
        }
        return null;
    }

    public function getColorSibling(int $eventId, string $productId): ?array
    {
        $siblingId = $this->productConfig[$productId]['color_sibling'] ?? null;
        if (!$siblingId) {
            return null;
        }
        return $this->getProduct($eventId, $siblingId);
    }

    /**
     * Produto irmão por faixa etária (Le Maillot Adulto ↔ Criança). Usado para
     * exibir o box "Également disponible" na página de detalhes — não é um
     * seletor: apenas indica e liga para a outra versão.
     */
    public function getAgeSibling(int $eventId, string $productId): ?array
    {
        $siblingId = $this->productConfig[$productId]['age_sibling'] ?? null;
        if (!$siblingId) {
            return null;
        }
        return $this->getProduct($eventId, $siblingId);
    }

    private function fetchSid(int $eventId): ?string
    {
        $html = $this->curlGet($this->checkoutBase . $eventId);
        if (!$html) {
            return null;
        }
        if (preg_match('/name=["\']sid["\'][^>]*value=["\']([^"\']+)["\']/', $html, $m)) {
            return $m[1];
        }
        return null;
    }

    private function fetchProductsHtml(int $eventId, string $sid): ?string
    {
        $url = $this->checkoutBase . $eventId . '/products';
        return $this->curlPost($url, ['sid' => $sid]);
    }

    private function parseProducts(string $html): array
    {
        $pattern = '/id="product_(\d+)_(\d+)"/';
        if (!preg_match_all($pattern, $html, $matches, PREG_OFFSET_CAPTURE)) {
            return [];
        }

        $offsets    = $matches[0];
        $productIds = $matches[1];
        $variantIds = $matches[2];

        $products = [];

        for ($i = 0; $i < count($offsets); $i++) {
            $productId = $productIds[$i][0];
            $variantId = $variantIds[$i][0];

            $start = $i > 0 ? $offsets[$i - 1][1] : 0;
            $end   = $offsets[$i][1];
            $block = substr($html, $start, $end - $start);
            $clean = preg_replace('/\s+/', ' ', $block);

            $name  = $this->extractName($clean);
            $price = $this->extractPrice($clean);
            $spans = $this->extractVariantSpans($clean);
            $dims  = $this->parseDimensions($spans, $variantId);

            if (!$name) {
                continue;
            }

            $nameLower = strtolower($name);
            if (str_contains($nameLower, 'promocional') ||
                str_contains($nameLower, 'voucher') ||
                str_contains($nameLower, 'coupon') ||
                str_contains($nameLower, 'código') ||
                str_contains($nameLower, 'code')) {
                continue;
            }

            $cfg = $this->productConfig[$productId] ?? [
                'image' => null,
                'type'  => 'accessory',
            ];

            if ($cfg['image'] === null) {
                continue;
            }

            // Extrai stock desta variante a partir do select logo após o id="product_X_Y"
            $selectStart = $offsets[$i][1];
            $selectEnd   = isset($offsets[$i + 1]) ? $offsets[$i + 1][1] : $selectStart + 600;
            $selectBlock = substr($html, $selectStart, $selectEnd - $selectStart);
            $stock       = $this->extractMaxStock($selectBlock);

            if (!isset($products[$productId])) {
                // Nome de exibição: apenas normaliza os espaços duplicados que o
                // LeBillet às vezes devolve (ex.: "Le Maillot  Adulto").
                $displayName = trim(preg_replace('/\s{2,}/', ' ', $name));

                $products[$productId] = [
                    'id'       => $productId,
                    'name'     => $displayName,
                    'price'    => $price,
                    'image'    => $cfg['image'],
                    'images'   => $cfg['images'] ?? [],
                    'type'     => $cfg['type'],
                    'color'    => $cfg['color'] ?? null,
                    'hidden'   => (bool) ($cfg['hidden'] ?? false),
                    'variants' => [],
                ];
            }

            // Rótulo de fallback: combina as dimensões detectadas (ex.: "Enfant S"),
            // ou cai no primeiro span limpo se nenhuma dimensão foi reconhecida.
            $labelParts = array_filter([$dims['group'], $dims['size']]);
            $label = $labelParts
                ? implode(' ', $labelParts)
                : ($this->cleanVariantLabel($spans[0] ?? null, $variantId) ?? '');

            $products[$productId]['variants'][] = [
                'id'    => $variantId,
                'label' => $label,
                'group' => $dims['group'],   // idade/género (Enfant, Adulte, Homme…) ou null
                'size'  => $dims['size'],    // tamanho (S, M, L, XL…) ou null
                'stock' => $stock,
                'price' => $price,
            ];
        }

        return array_values($products);
    }

    private function extractName(string $block): ?string
    {
        if (preg_match('/<h5[^>]*>\s*(.+?)\s*<\/h5>/i', $block, $m)) {
            $name = trim(strip_tags($m[1]));
            $name = html_entity_decode($name, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            if (strlen($name) > 2) {
                return $name;
            }
        }
        return null;
    }

    private function extractPrice(string $block): ?float
    {
        if (preg_match('/€\s*([\d]+)[,.](\d{2})/', $block, $m)) {
            return (float) ($m[1] . '.' . $m[2]);
        }
        return null;
    }

    private const SIZE_MAP = ['132' => 'S', '133' => 'M', '134' => 'L', '135' => 'XL'];

    private const IGNORED_LABELS = ['feminino', 'masculino', 'fille', 'garçon', 'garcon'];

    /** Palavras reconhecidas como dimensão de idade/género (grupo). */
    private const GROUP_WORDS = [
        'enfant', 'criança', 'crianca', 'kids', 'kid', 'child', 'children',
        'adulte', 'adulto', 'adult',
        'masculino', 'masculin', 'homme', 'men', 'man', 'male',
        'feminino', 'feminin', 'féminin', 'femme', 'women', 'woman', 'female',
        'fille', 'garçon', 'garcon', 'girl', 'boy', 'unisexe', 'unisex',
    ];

    /**
     * Devolve todos os <span> relevantes de um bloco de variante (sem preços,
     * sem "Lote/Lot N", já decodificados e normalizados).
     *
     * @return string[]
     */
    private function extractVariantSpans(string $block): array
    {
        $spans = [];
        if (preg_match_all('/<span[^>]*>\s*([^<]+?)\s*<\/span>/i', $block, $m)) {
            foreach ($m[1] as $candidate) {
                $candidate = html_entity_decode(trim(strip_tags($candidate)), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $candidate = trim(preg_replace('/[\x{00A0}\s]+/u', ' ', $candidate));
                if ($candidate === '' || str_starts_with($candidate, '€')) {
                    continue;
                }
                if (preg_match('/^lote?\b/i', $candidate)) { // "Lote 1" / "Lot 1"
                    continue;
                }
                $spans[] = $candidate;
            }
        }
        return $spans;
    }

    /**
     * Extrai as duas dimensões de uma variante a partir dos seus spans:
     *   - group: idade/género (Enfant, Adulte, Homme, Femme…) ou null
     *   - size:  tamanho (S, M, L, XL…) ou null
     *
     * Aceita spans separados ("Enfant", "TAMANHO S") ou combinados num só
     * ("Enfant - S"). Se o tamanho não vier no texto, tenta o SIZE_MAP por id.
     *
     * @param string[] $spans
     * @return array{group: ?string, size: ?string}
     */
    private function parseDimensions(array $spans, string $variantId): array
    {
        $group = null;
        $size  = null;

        foreach ($spans as $span) {
            // Remove apenas o rótulo do campo ("TAMANHO S" → "S"). O que sobra é
            // o tamanho EXATAMENTE como vem do LeBillet, em qualquer formato
            // ("S", "XXL", "3-4 ANS", "Unique"…). Não validamos nem normalizamos.
            $clean = preg_replace('/\b(TAMANHO|TAILLE|SIZE|TALLA|TAGLIA)\b\s*/i', '', $span);
            $clean = trim(preg_replace('/\s+/', ' ', $clean));
            if ($clean === '') {
                continue;
            }

            // Se o span for só uma palavra de género/idade, é o "grupo" (ex.: o
            // "Feminino" redundante do Sale Gosse) — separamos para não virar
            // tamanho. Caso contrário, é o tamanho, pego como veio.
            if (in_array(mb_strtolower($clean), self::GROUP_WORDS, true)) {
                if ($group === null) {
                    $group = $clean;
                }
                continue;
            }
            if ($size === null) {
                $size = $clean;
            }
        }

        // Fallback de segurança quando a variante não traz texto de tamanho.
        if ($size === null && isset(self::SIZE_MAP[$variantId])) {
            $size = self::SIZE_MAP[$variantId];
        }

        return ['group' => $group, 'size' => $size];
    }

    private function cleanVariantLabel(?string $raw, string $variantId = ''): ?string
    {
        if ($raw === null || trim($raw) === '') {
            return self::SIZE_MAP[$variantId] ?? null;
        }
        $label = trim($raw);
        $label = preg_replace('/^TAMANHO\s+/i', '', $label);
        $label = preg_replace('/^TAILLE\s+/i', '', $label);
        $label = preg_replace('/^SIZE\s+/i', '', $label);
        $label = trim($label);

        $normalized = strtolower(trim(preg_replace('/[\x{00A0}\s]+/u', ' ', $label)));
        if (in_array($normalized, self::IGNORED_LABELS, true)) {
            return self::SIZE_MAP[$variantId] ?? null;
        }

        return $label !== '' ? $label : (self::SIZE_MAP[$variantId] ?? null);
    }

    /**
     * Retorna a quantidade máxima disponível para uma variante,
     * lendo as opções do <select> logo após o id="product_X_Y".
     * Retorna 0 se esgotado, ou 10 como fallback.
     */
    private function extractMaxStock(string $block): int
    {
        if (preg_match_all('/<option value="(\d+)"/', $block, $m)) {
            return max(array_map('intval', $m[1]));
        }
        return 10;
    }

    private function curlGet(string $url): ?string
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_USERAGENT      => 'Mozilla/5.0 (compatible; Yanns/1.0)',
        ]);
        $result = curl_exec($ch);
        return ($result !== false && $result !== '') ? $result : null;
    }

    private function curlPost(string $url, array $fields = []): ?string
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($fields),
            CURLOPT_USERAGENT      => 'Mozilla/5.0 (compatible; Yanns/1.0)',
        ]);
        $result = curl_exec($ch);
        return ($result !== false && $result !== '') ? $result : null;
    }

    private function getCache(string $key): ?array
    {
        $file = $this->cacheDir . md5($key) . '.json';
        if (!file_exists($file)) {
            return null;
        }
        if (time() - filemtime($file) > $this->cacheTtl) {
            @unlink($file);
            return null;
        }
        $data = json_decode(file_get_contents($file), true);
        return is_array($data) ? $data : null;
    }

    private function setCache(string $key, array $data): void
    {
        $file = $this->cacheDir . md5($key) . '.json';
        file_put_contents($file, json_encode($data));
    }
}
