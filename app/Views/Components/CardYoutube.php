<?php
$videoId = $videoId ?? 'mwoHkRVOu7g';
$title = $title ?? 'Yanns - Toké Toké (Clip officiel)';
$meta = $meta ?? '19 k vues · il y a 6 jours';
$imageUrl = $imageUrl ?? "https://img.youtube.com/vi/{$videoId}/maxresdefault.jpg";
$class = $class ?? '';

$cardClass = 'card-youtube';
if ($class) {
    $cardClass .= ' ' . $class;
}
?>
<div class="<?= htmlspecialchars($cardClass)?>" data-video-id="<?= htmlspecialchars($videoId)?>">
    <div class="card-youtube-body">
        <div class="card-youtube-thumb">
            <div class="card-youtube-image">
                <img src="<?= htmlspecialchars($imageUrl)?>" alt="<?= htmlspecialchars($title)?>" />
            </div>
            <div class="card-youtube-overlay"></div>
            <div class="card-youtube-play">
                <img src="/assets/icons/icon-play.svg" alt="Play" />
            </div>
        </div>
        <div class="card-youtube-info">
            <p class="card-youtube-title">
                <?= htmlspecialchars($title)?>
            </p>
            <p class="card-youtube-meta">
                <?= htmlspecialchars($meta)?>
            </p>
        </div>
    </div>
</div>