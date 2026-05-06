<?php
// Props: service ('spotify', 'deezer', 'apple', 'youtube', 'amazon')
$service = strtolower($service ?? 'spotify');
$serviceNames = [
    'spotify' => 'Spotify',
    'deezer' => 'Deezer',
    'apple' => 'Apple Music',
    'youtube' => 'YouTube Music',
    'amazon' => 'Amazon Music'
];
$name = $serviceNames[$service] ?? ucfirst($service);

// Icon mapping to new brand methods
$methodMap = [
    'spotify' => 'brandSpotify',
    'deezer' => 'brandDeezer',
    'apple' => 'brandApple',
    'youtube' => 'brandYoutube',
    'amazon' => 'brandAmazon'
];
$iconMethod = $methodMap[$service] ?? 'brandSpotify'; // Fallback
$url = $url ?? '#';
?>
<a href="<?= $url?>" target="_blank" rel="noopener noreferrer" class="music-service-button" data-service="<?= $service?>" aria-label="Listen on <?= $name?>">
    <div class="music-service-icon">
        <?=\App\Helpers\IconHelper::$iconMethod(32, 'var(--color-neutral-0)')?>
    </div>
    <div class="music-service-content">
        <p class="music-service-label">Listen on</p>
        <p class="music-service-name">
            <?= $name?>
        </p>
    </div>
</a>