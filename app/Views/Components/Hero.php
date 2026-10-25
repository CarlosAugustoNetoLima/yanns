<?php
/**
 * Hero Component
 */
?>
<section class="hero" aria-label="Hero Section">
    <div class="hero-video-container">
        <!-- Video will go here -->
        <video id="heroVideo" autoplay loop playsinline muted preload="auto"
               poster="assets/video/hero-poster.webp" class="hero-video">
            <source src="assets/video/Yanns - Coco Choco.optimized.mp4" type="video/mp4">
        </video>
    </div>

    <div class="hero-filter-layer">
        <img src="assets/img/Filter layer.svg" alt="" class="hero-filter-image">
    </div>

    <!-- Apps sound left -->
    <div class="hero-apps-sound">
        <?= \App\Helpers\Component::render('MusicServiceButton', ['service' => 'youtube', 'url' => 'https://www.youtube.com/channel/UCpbRjfY2hvgKJBJkOsvzuMw']) ?>
        <?= \App\Helpers\Component::render('MusicServiceButton', ['service' => 'apple', 'url' => 'https://music.apple.com/no/artist/yanns/1176647388']) ?>
        <?= \App\Helpers\Component::render('MusicServiceButton', ['service' => 'spotify', 'url' => 'https://open.spotify.com/intl-pt/artist/7LPPIN97GR0FvlNwBufbyp']) ?>
        <?= \App\Helpers\Component::render('MusicServiceButton', ['service' => 'amazon', 'url' => 'https://music.amazon.com.br/artists/B01IPN06AW/yanns']) ?>
        <?= \App\Helpers\Component::render('MusicServiceButton', ['service' => 'deezer', 'url' => 'https://www.deezer.com/br/artist/10725097']) ?>
    </div>

    <!-- Control players right -->
    <div class="hero-control-players">
        <?= \App\Helpers\Component::render('VideoControl', ['state' => 'pause']) ?>
        <?= \App\Helpers\Component::render('SoundControl', ['state' => 'muted']) ?>
    </div>

    <!-- Scroll icon bottom center -->
    <div class="hero-scroll">
        <?= \App\Helpers\Component::render('ScrollIcon') ?>
    </div>
</section>