<?php
use App\Helpers\Component;

$videosVus = [
    [
        'videoId' => 'SQKHkQAVoO8',
        'title' => 'Yanns raconte son histoire (Interview Officielle)',
        'meta' => '19 k vues · il y a 6 jours',
        'imageUrl' => 'https://img.youtube.com/vi/SQKHkQAVoO8/maxresdefault.jpg'
    ],
    [
        'videoId' => '988rwXMSPic',
        'title' => 'Yanns - Coco Choco (Clip officiel)',
        'meta' => '551 k vues · il y a 1 mois',
        'imageUrl' => 'https://img.youtube.com/vi/988rwXMSPic/maxresdefault.jpg'
    ],
    [
        'videoId' => 'cr21ydPCLv8',
        'title' => 'Interview Coco choco 🥥',
        'meta' => '16 k vues · il y a 2 mois',
        'imageUrl' => 'https://img.youtube.com/vi/cr21ydPCLv8/maxresdefault.jpg'
    ],
    [
        'videoId' => '7oAZu8dehAo',
        'title' => 'Parfum 🌹Mon chouchou 🌹(en exclu pour la tournée 20025/26 🎫',
        'meta' => '15 k vues · il y a 2 mois',
        'imageUrl' => 'https://img.youtube.com/vi/7oAZu8dehAo/maxresdefault.jpg'
    ]
];

$videosRecents = $videosVus; // For now, use the same videos or adjust as needed, but ensuring 4 videos.
?>
<section class="videos-section">
    <div class="videos-row videos-vus-row">
        <div class="videos-title videos-title-left" data-name="Title">
            <div class="videos-title-image img-les" data-name="les">
                <img alt="LES" src="/assets/icons/les.svg" />
            </div>
            <div class="videos-title-image img-plus" data-name="plus">
                <img alt="PLUS" src="/assets/icons/plus.svg" />
            </div>
            <div class="videos-title-image img-vus" data-name="vus">
                <img alt="VUS" src="/assets/icons/vus.svg" />
            </div>
        </div>
        <div class="videos-cards">
            <?php foreach ($videosVus as $video): ?>
            <?= Component::render('CardYoutube', $video)?>
            <?php
endforeach; ?>
        </div>
    </div>

    <div class="videos-row videos-recents-row">
        <div class="videos-cards">
            <?php foreach ($videosRecents as $video): ?>
            <?= Component::render('CardYoutube', $video)?>
            <?php
endforeach; ?>
        </div>
        <div class="videos-title videos-title-right" data-name="Title">
            <div class="videos-title-image img-les" data-name="les">
                <img alt="LES" src="/assets/icons/les.svg" />
            </div>
            <div class="videos-title-image img-plus" data-name="plus">
                <img alt="PLUS" src="/assets/icons/plus.svg" />
            </div>
            <div class="videos-title-image img-recents" data-name="recents">
                <img alt="RÉCENTS" src="/assets/icons/recents.svg" />
            </div>
        </div>
    </div>
</section>