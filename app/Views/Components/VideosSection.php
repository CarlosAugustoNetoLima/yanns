<?php
use App\Helpers\Component;
use App\Helpers\I18n;

/**
 * Vídeos carregados dinamicamente via YouTube Data API v3.
 * Quando a API não está configurada ou falha, usa dados hardcoded como fallback.
 *
 * Cada vídeo guarda views como inteiro e tempo como (valor + unidade) — a
 * formatação por idioma acontece no render abaixo.
 *   time_unit: 'day' | 'month' | 'year'
 */

$ytService = new \App\Services\YouTubeService();

// Mais vistos — refletindo a aba Popular do canal no YouTube
$videosVus = $ytService->getMostViewedVideos(4);

// Mais recentes — ordem real do canal
$videosRecents = $ytService->getRecentVideos(4);

/**
 * Compõe meta localizada: "1,6 M vues · il y a 2 mois", "1.6M views · 2 months ago", etc.
 */
$buildMeta = function (array $v): string {
    $views = I18n::formatViews((int) $v['views_count']) . ' ' . I18n::t('videos.label_views');
    $when  = I18n::formatTimeAgo((int) $v['time_value'], $v['time_unit']);
    return $views . ' · ' . $when;
};

$prepareCard = function (array $v) use ($buildMeta): array {
    return [
        'videoId'  => $v['videoId'],
        'title'    => $v['title'],
        'meta'     => $buildMeta($v),
        'imageUrl' => $v['imageUrl'],
    ];
};
?>
<section class="videos-section">
    <!-- LES PLUS RÉCENTS (top) -->
    <div class="videos-row videos-recents-row">
        <div class="videos-title videos-title-left" data-name="Title">
            <div class="videos-title-image img-les" data-name="les">
                <img alt="LES" src="/assets/img/les.svg" />
            </div>
            <div class="videos-title-image img-plus" data-name="plus">
                <img alt="PLUS" src="/assets/img/plus.svg" />
            </div>
            <div class="videos-title-image img-recents" data-name="recents">
                <img alt="RÉCENTS" src="/assets/img/recents.svg" />
            </div>
        </div>
        <div class="videos-cards">
            <?php foreach ($videosRecents as $video): ?>
                <?= Component::render('CardYoutube', $prepareCard($video)) ?>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- LES PLUS VUS (bottom) -->
    <div class="videos-row videos-vus-row">
        <div class="videos-cards">
            <?php foreach ($videosVus as $video): ?>
                <?= Component::render('CardYoutube', $prepareCard($video)) ?>
            <?php endforeach; ?>
        </div>
        <div class="videos-title videos-title-right" data-name="Title">
            <div class="videos-title-image img-les" data-name="les">
                <img alt="LES" src="/assets/img/les.svg" />
            </div>
            <div class="videos-title-image img-plus" data-name="plus">
                <img alt="PLUS" src="/assets/img/plus.svg" />
            </div>
            <div class="videos-title-image img-vus" data-name="vus">
                <img alt="VUS" src="/assets/img/vus.svg" />
            </div>
        </div>
    </div>
</section>
