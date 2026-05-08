<?php
use App\Helpers\Component;
use App\Helpers\I18n;

/**
 * Cada vídeo guarda views como inteiro e tempo como (valor + unidade) — a
 * formatação por idioma acontece no render abaixo.
 *   time_unit: 'day' | 'month' | 'year'
 */

// Mais vistos — refletindo a aba Popular do canal no YouTube
$videosVus = [
    [
        'videoId'      => 'MmQa-lLy38o',
        'title'        => 'Yanns - Clic clic pan pan (Clip Officiel)',
        'views_count'  => 176_365_394,
        'time_value'   => 4,
        'time_unit'    => 'year',
        'imageUrl'     => 'https://img.youtube.com/vi/MmQa-lLy38o/maxresdefault.jpg',
    ],
    [
        'videoId'      => 'OUhlSwULEBE',
        'title'        => "Yanns - Mains en L'air (Clip Officiel)",
        'views_count'  => 32_458_131,
        'time_value'   => 3,
        'time_unit'    => 'year',
        'imageUrl'     => 'https://img.youtube.com/vi/OUhlSwULEBE/maxresdefault.jpg',
    ],
    [
        'videoId'      => 'LzEwOWyvW68',
        'title'        => 'Yanns - En détresse (Clip Officiel)',
        'views_count'  => 29_757_941,
        'time_value'   => 4,
        'time_unit'    => 'year',
        'imageUrl'     => 'https://img.youtube.com/vi/LzEwOWyvW68/maxresdefault.jpg',
    ],
    [
        'videoId'      => 'E6rYfBcVxUw',
        'title'        => 'Yanns - Soleil et nanas (Clip Officiel)',
        'views_count'  => 26_385_648,
        'time_value'   => 3,
        'time_unit'    => 'year',
        'imageUrl'     => 'https://img.youtube.com/vi/E6rYfBcVxUw/maxresdefault.jpg',
    ],
];

// Mais recentes — ordem real do canal
$videosRecents = [
    [
        'videoId'      => 'Uy-L1wnHy7U',
        'title'        => 'OLYMPIA 2026',
        'views_count'  => 6_200,
        'time_value'   => 11,
        'time_unit'    => 'day',
        'imageUrl'     => 'https://img.youtube.com/vi/Uy-L1wnHy7U/maxresdefault.jpg',
    ],
    [
        'videoId'      => '8fxLKm-mgb4',
        'title'        => 'YANNS - MYTHO (Clip officiel)',
        'views_count'  => 578_000,
        'time_value'   => 1,
        'time_unit'    => 'month',
        'imageUrl'     => 'https://img.youtube.com/vi/8fxLKm-mgb4/sddefault.jpg',
    ],
    [
        'videoId'      => 'mwoHkRVOu7g',
        'title'        => 'YANNS - TOKÉ TOKÉ (Clip officiel)',
        'views_count'  => 1_600_000,
        'time_value'   => 2,
        'time_unit'    => 'month',
        'imageUrl'     => 'https://img.youtube.com/vi/mwoHkRVOu7g/maxresdefault.jpg',
    ],
    [
        'videoId'      => 'NVPeityE_bY',
        'title'        => 'YANNS - TOKÉ TOKÉ (Lyrics Vidéo)',
        'views_count'  => 187_000,
        'time_value'   => 2,
        'time_unit'    => 'month',
        'imageUrl'     => 'https://img.youtube.com/vi/NVPeityE_bY/maxresdefault.jpg',
    ],
];

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
