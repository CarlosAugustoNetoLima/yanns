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

// Featured: último clip oficial (Mytho)
$featured = [
    'videoId'     => '8fxLKm-mgb4',
    'title'       => 'YANNS - MYTHO (Clip officiel)',
    'views_count' => 578_000,
    'time_value'  => 1,
    'time_unit'   => 'month',
    'imageUrl'    => 'https://img.youtube.com/vi/8fxLKm-mgb4/sddefault.jpg',
];

// Catálogo de clipes oficiais (8) — ordenado por views descendente
$clips = [
    ['videoId' => 'MmQa-lLy38o', 'title' => 'Yanns - Clic clic pan pan (Clip Officiel)',  'views_count' => 176_365_394, 'time_value' => 4, 'time_unit' => 'year',  'imageUrl' => 'https://img.youtube.com/vi/MmQa-lLy38o/maxresdefault.jpg'],
    ['videoId' => 'OUhlSwULEBE', 'title' => "Yanns - Mains en L'air (Clip Officiel)",     'views_count' => 32_458_131,  'time_value' => 3, 'time_unit' => 'year',  'imageUrl' => 'https://img.youtube.com/vi/OUhlSwULEBE/maxresdefault.jpg'],
    ['videoId' => 'LzEwOWyvW68', 'title' => 'Yanns - En détresse (Clip Officiel)',        'views_count' => 29_757_941,  'time_value' => 4, 'time_unit' => 'year',  'imageUrl' => 'https://img.youtube.com/vi/LzEwOWyvW68/maxresdefault.jpg'],
    ['videoId' => 'E6rYfBcVxUw', 'title' => 'Yanns - Soleil et nanas (Clip Officiel)',    'views_count' => 26_385_648,  'time_value' => 3, 'time_unit' => 'year',  'imageUrl' => 'https://img.youtube.com/vi/E6rYfBcVxUw/maxresdefault.jpg'],
    ['videoId' => 'WRRVi5aNzQk', 'title' => "YANNS - JE T'AIME (clip officiel)",          'views_count' => 5_030_466,   'time_value' => 2, 'time_unit' => 'year',  'imageUrl' => 'https://img.youtube.com/vi/WRRVi5aNzQk/maxresdefault.jpg'],
    ['videoId' => 'mwoHkRVOu7g', 'title' => 'YANNS - TOKÉ TOKÉ (Clip officiel)',          'views_count' => 1_667_370,   'time_value' => 2, 'time_unit' => 'month', 'imageUrl' => 'https://img.youtube.com/vi/mwoHkRVOu7g/maxresdefault.jpg'],
    ['videoId' => '988rwXMSPic', 'title' => 'YANNS - COCO CHOCO (Clip officiel)',         'views_count' => 986_394,     'time_value' => 5, 'time_unit' => 'month', 'imageUrl' => 'https://img.youtube.com/vi/988rwXMSPic/maxresdefault.jpg'],
    ['videoId' => '8fxLKm-mgb4', 'title' => 'YANNS - MYTHO (Clip officiel)',              'views_count' => 578_021,     'time_value' => 1, 'time_unit' => 'month', 'imageUrl' => 'https://img.youtube.com/vi/8fxLKm-mgb4/sddefault.jpg'],
];

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
