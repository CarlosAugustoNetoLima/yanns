<?php
/**
 * VideoControl Component
 * 
 * Props:
 * - state: 'play' | 'pause' (default: 'play')
 */
$state = $state ?? 'play';
$label = ucfirst($state);
?>
<button class="video-control state-<?= $state?>" aria-label="<?= $label?> Video" data-state="<?= $state?>"
    onclick="toggleVideo(this)">
    <span class="icon-play" style="<?= $state === 'play' ? '' : 'display: none;'?>">
        <?=\App\Helpers\IconHelper::controlPlay(24)?>
    </span>
    <span class="icon-pause" style="<?= $state === 'pause' ? '' : 'display: none;'?>">
        <?=\App\Helpers\IconHelper::controlPause(24)?>
    </span>
</button>