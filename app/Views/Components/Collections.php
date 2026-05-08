<?php
use App\Helpers\I18n;
?>
<section class="collections-section">
    <div class="collections-container">

        <!-- Grid (populated by JS from API) -->
        <div class="collections-grid" id="collections-grid"
             data-msg-loading="<?= htmlspecialchars(I18n::t('collections.loading')) ?>"
             data-msg-error="<?= htmlspecialchars(I18n::t('collections.error')) ?>"
             data-msg-empty="<?= htmlspecialchars(I18n::t('collections.empty')) ?>"
             data-msg-no-image="<?= htmlspecialchars(I18n::t('product.image_unavailable')) ?>">
            <div class="collections-loading"><?= htmlspecialchars(I18n::t('collections.loading')) ?></div>
        </div>

    </div>
</section>
