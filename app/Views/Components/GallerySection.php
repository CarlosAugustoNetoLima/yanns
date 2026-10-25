<?php
use App\Helpers\I18n;

// image number → product ID (null = sem link de produto)
// 38.png aparece nos dois grupos; usamos 10950 (primeira menção)
$galleryMapping = [
     1 => '12616',  2 => '12616',  3 => '12616',  4 => '12616',  5 => '12616',
     6 => '12616',  7 => '12616',  8 => '12616',  9 => '12616', 10 => '12616',
    11 => '12616', 12 => '12616', 13 => '12616', 14 => '12616', 15 => '12616',
    16 => '12616', 17 => '12616', 18 => '12616', 19 => '12616', 20 => '12616',
    21 => '12616', 22 => '12616', 23 => '12616',
    24 => '10949',
    25 => '10950',
    26 => null,
    27 => '10950', 28 => '10950', 29 => '10950',
    30 => null,
    31 => '10949',
    32 => '10947', 33 => '10947',
    34 => null,
    35 => '10947',
    36 => '10949', 37 => '10949',
    38 => '10950',
    39 => '10950',
    40 => '10949',
];
?>

<section class="gallery-section">
    <div class="gallery-grid">
        <?php for ($i = 1; $i <= 40; $i++): ?>
            <?php
            $productId = $galleryMapping[$i] ?? null;
            $src = "/assets/img/yanns/gallery/{$i}.webp";
            $href = $productId ? "/produto?id={$productId}" : null;
            ?>
            <?php if ($href): ?>
                <a class="gallery-item gallery-item--linked"
                   href="<?= htmlspecialchars($href) ?>"
                   aria-label="Voir le produit">
                    <img src="<?= htmlspecialchars($src) ?>"
                         alt=""
                         loading="<?= $i <= 8 ? 'eager' : 'lazy' ?>">
                    <span class="gallery-item-overlay" aria-hidden="true">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <line x1="3" y1="6" x2="21" y2="6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            <path d="M16 10a4 4 0 01-8 0" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                </a>
            <?php else: ?>
                <div class="gallery-item">
                    <img src="<?= htmlspecialchars($src) ?>"
                         alt=""
                         loading="lazy">
                </div>
            <?php endif; ?>
        <?php endfor; ?>
    </div>
</section>
