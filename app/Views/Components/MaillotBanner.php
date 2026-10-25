<?php

use App\Helpers\I18n;

/**
 * Banner "Le Maillot" — colagem de fotos exibida entre o menu e a grelha
 * de produtos na página /collections. Liga para a página do produto 12616.
 */

$images = [
    '/assets/img/yanns/gallery/1.webp',
    '/assets/img/yanns/gallery/10.webp',
    '/assets/img/yanns/gallery/5.webp',
];
?>
<section class="maillot-banner">
    <a class="maillot-banner-link" href="/produto?id=12616" aria-label="<?= htmlspecialchars(I18n::t('maillot_banner.title')) ?>">
        <div class="maillot-banner-collage" aria-hidden="true">
            <img class="maillot-banner-img" src="<?= htmlspecialchars($images[0]) ?>" alt="" loading="lazy">
            <img class="maillot-banner-img is-center" src="<?= htmlspecialchars($images[1]) ?>" alt="" loading="eager">
            <img class="maillot-banner-img" src="<?= htmlspecialchars($images[2]) ?>" alt="" loading="lazy">
        </div>
        <div class="maillot-banner-content">
            <span class="maillot-banner-label"><?= htmlspecialchars(I18n::t('maillot_banner.label')) ?></span>
            <h2 class="maillot-banner-title">
                <img src="/assets/img/collections/LE MAILLOT.png" alt="<?= htmlspecialchars(I18n::t('maillot_banner.title')) ?>" class="maillot-banner-title-logo">
            </h2>
            <span class="maillot-banner-cta"><?= htmlspecialchars(I18n::t('maillot_banner.cta')) ?></span>
        </div>
    </a>
</section>
