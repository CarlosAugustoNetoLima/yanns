<?php
/**
 * Hero Component
 */
?>
<section class="hero" aria-label="Hero Section">
    <div class="hero-video-container">
        <!-- Video will go here -->
        <video id="heroVideo" autoplay loop playsinline class="hero-video">
            <source src="assets/video/Yanns - Coco Choco.mp4" type="video/mp4">
        </video>
    </div>

    <div class="hero-filter-layer">
        <img src="assets/img/Filter layer.svg" alt="" class="hero-filter-image">
    </div>

    <!-- Apps sound left -->
    <div class="hero-apps-sound">
        <?=\App\Helpers\Component::render('MusicServiceButton', ['service' => 'youtube'])?>
        <?=\App\Helpers\Component::render('MusicServiceButton', ['service' => 'apple'])?>
        <?=\App\Helpers\Component::render('MusicServiceButton', ['service' => 'spotify'])?>
        <?=\App\Helpers\Component::render('MusicServiceButton', ['service' => 'amazon'])?>
        <?=\App\Helpers\Component::render('MusicServiceButton', ['service' => 'deezer'])?>
    </div>

    <!-- Control players right -->
    <div class="hero-control-players">
        <?=\App\Helpers\Component::render('VideoControl', ['state' => 'pause'])?>
        <?=\App\Helpers\Component::render('SoundControl', ['state' => 'unmuted'])?>
    </div>

    <!-- Scroll icon bottom center -->
    <div class="hero-scroll">
        <?=\App\Helpers\Component::render('ScrollIcon')?>
    </div>
</section>