<?php
use App\Helpers\Component;
use App\Helpers\I18n;

$tabs = [
    'ALL',
];

?>
<section class="collections-section">
    <div class="collections-container">

        <!-- Tabs -->
        <div class="collections-tabs" id="collections-tabs">
            <?php foreach ($tabs as $index => $tab): ?>
            <?= Component::render('Tab', [
        'label' => $tab,
        'active' => $index === 0,
        'class' => 'collection-tab-item'
    ])?>
            <?php
endforeach; ?>
        </div>

        <!-- Grid (Will be populated by JS from API) -->
        <div class="collections-grid" id="collections-grid"
             data-msg-loading="<?= htmlspecialchars(I18n::t('collections.loading')) ?>"
             data-msg-error="<?= htmlspecialchars(I18n::t('collections.error')) ?>"
             data-msg-empty="<?= htmlspecialchars(I18n::t('collections.empty')) ?>"
             data-msg-no-image="<?= htmlspecialchars(I18n::t('product.image_unavailable')) ?>">
            <div class="collections-loading"><?= htmlspecialchars(I18n::t('collections.loading')) ?></div>
        </div>

    </div>
</section>
