<?php
use App\Helpers\Component;
use App\Helpers\I18n;
use App\Helpers\IconHelper;

// URLs das plataformas (mesmas do Hero da home)
$platforms = [
    ['service' => 'spotify', 'url' => 'https://open.spotify.com/intl-pt/artist/7LPPIN97GR0FvlNwBufbyp'],
    ['service' => 'apple',   'url' => 'https://music.apple.com/no/artist/yanns/1176647388'],
    ['service' => 'youtube', 'url' => 'https://www.youtube.com/channel/UCpbRjfY2hvgKJBJkOsvzuMw'],
    ['service' => 'deezer',  'url' => 'https://www.deezer.com/br/artist/10725097'],
    ['service' => 'amazon',  'url' => 'https://music.amazon.com.br/artists/B01IPN06AW/yanns'],
];

// Vídeos carregados dinamicamente via YouTube Data API v3
$ytService = new \App\Services\YouTubeService();

// Featured: último clip oficial
$featured = $ytService->getLatestClip();

// Catálogo de clipes oficiais — ordenado por views descendente
$clips = $ytService->getAllClips(8);

// Helper: meta localizada para CardYoutube
$buildMeta = function (array $v): string {
    return I18n::formatViews((int) $v['views_count']) . ' ' . I18n::t('videos.label_views')
        . ' · ' . I18n::formatTimeAgo((int) $v['time_value'], $v['time_unit']);
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

<section class="music-page">

    <!-- HERO -->
    <div class="music-hero">
        <div class="music-hero-bg" aria-hidden="true">
            <span class="music-bg-logo"><?= IconHelper::logo(200, 'currentColor') ?></span>
        </div>
        <div class="music-hero-inner">
            <h1 class="music-hero-title"><?= htmlspecialchars(I18n::t('music.title')) ?></h1>
            <p class="music-hero-subtitle"><?= htmlspecialchars(I18n::t('music.subtitle')) ?></p>
            <div class="music-hero-platforms">
                <?php foreach ($platforms as $p): ?>
                    <?= Component::render('MusicServiceButton', ['service' => $p['service'], 'url' => $p['url']]) ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- DERNIER CLIP -->
    <div class="music-featured">
        <div class="music-featured-inner">
            <div class="music-featured-info">
                <span class="bio-section-label"><?= htmlspecialchars(I18n::t('music.featured_label')) ?></span>
                <h2 class="music-featured-title"><?= htmlspecialchars($featured['title']) ?></h2>
                <p class="music-featured-meta"><?= htmlspecialchars($buildMeta($featured)) ?></p>
                <div class="music-featured-cta">
                    <button
                        type="button"
                        class="btn btn-style-solid btn-variant-primary btn-size-extra-large"
                        data-video-id="<?= htmlspecialchars($featured['videoId']) ?>"
                        onclick="(function(){ const c = document.querySelector('.card-youtube[data-video-id=&quot;<?= htmlspecialchars($featured['videoId']) ?>&quot;]'); if (c) c.click(); })();"
                    ><?= htmlspecialchars(I18n::t('music.featured_watch')) ?></button>
                </div>
            </div>
            <div class="music-featured-thumb">
                <?= Component::render('CardYoutube', $prepareCard($featured)) ?>
            </div>
        </div>
    </div>

    <!-- TOUS LES CLIPS OFFICIELS -->
    <div class="music-clips">
        <div class="music-clips-inner">
            <span class="bio-section-label"><?= htmlspecialchars(I18n::t('music.clips_label')) ?></span>
            <div class="music-clips-grid">
                <?php foreach ($clips as $clip): ?>
                    <?= Component::render('CardYoutube', $prepareCard($clip)) ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- ÉCOUTEZ PARTOUT -->
    <div class="music-streaming">
        <div class="music-streaming-inner">
            <span class="bio-section-label bio-section-label-light"><?= htmlspecialchars(I18n::t('music.streaming_label')) ?></span>
            <h2 class="music-streaming-title"><?= htmlspecialchars(I18n::t('music.streaming_title')) ?></h2>
            <p class="music-streaming-subtitle"><?= htmlspecialchars(I18n::t('music.streaming_subtitle')) ?></p>
            <div class="music-streaming-platforms">
                <?php foreach ($platforms as $p): ?>
                    <?= Component::render('MusicServiceButton', ['service' => $p['service'], 'url' => $p['url']]) ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

</section>
