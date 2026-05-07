<?php
use App\Helpers\Component;
use App\Helpers\I18n;

?>
<section class="boutique-section" data-name="Boutique section" id="boutique">
    <div class="boutique-container" data-name="container">
        <div class="boutique-text" data-name="text">
            <h2 class="boutique-heading" data-name="Paragraph container">
                <span class="boutique-heading-pink"><?= htmlspecialchars(I18n::t('boutique.heading_pink')) ?></span>
                <span class="boutique-heading-black"><?= htmlspecialchars(I18n::t('boutique.heading_black')) ?></span>
            </h2>
            <div class="boutique-action">
                <?= Component::render('Button', [
    'label' => I18n::t('boutique.discover'),
    'variant' => 'primary',
    'style' => 'solid',
    'size' => 'extra-large',
    'url' => '/collections'
])?>
            </div>
        </div>
        <div class="boutique-image-wrapper" data-name="Image">
            <img src="/assets/img/boutique-collage-mobile.png" alt="Boutique collage" class="boutique-image mobile-only">
            <img src="/assets/img/boutique-collage.png" alt="Boutique collage" class="boutique-image desktop-only">
        </div>
    </div>
</section>