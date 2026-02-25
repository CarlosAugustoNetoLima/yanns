<?php
// Props: image, title, price
// Default image fallback if none provided
$image = $image ?? 'assets/images/placeholder-cap.png';
$title = $title ?? 'PRODUCT NAME';
$price = $price ?? '0,00 €';
?>
<div class="store-card">
    <div class="store-card-image">
        <!-- Using a placeholder if actual image path isn't valid in styleguide -->
        <img src="<?= $image?>" alt="<?= htmlspecialchars($title)?>">
    </div>
    <div class="store-card-content">
        <div class="store-card-title">
            <p>
                <?= $title?>
            </p>
        </div>
        <p class="store-card-price">
            <?= $price?>
        </p>
    </div>
</div>