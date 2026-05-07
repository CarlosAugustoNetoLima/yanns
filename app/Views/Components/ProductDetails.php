<?php

use App\Helpers\Component;
use App\Helpers\I18n;

/** @var array|null $product */
/** @var array|null $sibling */
/** @var int $eventId */
/** @var string $checkoutUrl */

if (!isset($product) || !$product) {
    ?>
    <section class="product-details-section">
        <div class="product-details-container">
            <a href="/collections" class="product-details-back">
                <?= \App\Helpers\IconHelper::keyboardReturn(16, 'var(--color-primary-400-main)') ?>
                <span><?= htmlspecialchars(I18n::t('product.return')) ?></span>
            </a>
            <h1 class="product-title"><?= htmlspecialchars(I18n::t('product.unavailable_title')) ?></h1>
            <p><?= htmlspecialchars(I18n::t('product.unavailable_text')) ?></p>
        </div>
    </section>
    <?php
    return;
}

$variants    = $product['variants'] ?? [];
$type        = $product['type'] ?? 'accessory';
$priceLabel  = number_format((float) $product['price'], 2, ',', '.') . ' €';
$showSizes   = $type === 'shirt' && count($variants) > 1;
$showColors  = $type === 'casquette' && $sibling !== null;
$defaultVariantId = $variants[0]['id'] ?? '1';

?>

<section class="product-details-section"
         data-product-id="<?= htmlspecialchars($product['id']) ?>"
         data-event-id="<?= htmlspecialchars((string) $eventId) ?>"
         data-checkout-url="<?= htmlspecialchars($checkoutUrl) ?>"
         data-default-variant="<?= htmlspecialchars($defaultVariantId) ?>"
         data-modal-title="<?= htmlspecialchars(I18n::t('product.modal_title')) ?>">
    <div class="product-details-container">
        <a href="/collections" class="product-details-back">
            <?= \App\Helpers\IconHelper::keyboardReturn(16, 'var(--color-primary-400-main)') ?>
            <span><?= htmlspecialchars(I18n::t('product.return')) ?></span>
        </a>

        <div class="product-details-grid">

            <div class="product-details-gallery">
                <?php if (!empty($product['image'])): ?>
                    <div class="product-details-thumbnails">
                        <div class="thumbnail active">
                            <img src="<?= htmlspecialchars($product['image']) ?>" alt="Thumbnail">
                        </div>
                    </div>
                    <div class="product-details-main-image">
                        <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                    </div>
                <?php else: ?>
                    <div class="product-details-main-image product-details-no-image">
                        <span><?= htmlspecialchars(I18n::t('product.image_unavailable')) ?></span>
                    </div>
                <?php endif; ?>
            </div>

            <div class="product-details-info">

                <div class="product-details-header">
                    <h1 class="product-title"><?= htmlspecialchars($product['name']) ?></h1>
                    <p class="product-price"><?= htmlspecialchars($priceLabel) ?></p>
                </div>

                <?php if ($showSizes): ?>
                    <div class="product-details-sizes">
                        <?php foreach ($variants as $i => $variant): ?>
                            <div
                                class="size-button<?= $i === 0 ? ' active' : '' ?>"
                                data-variant-id="<?= htmlspecialchars($variant['id']) ?>"
                                onclick="sizeSelect(this)"
                            >
                                <?= htmlspecialchars($variant['label']) ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if ($showColors): ?>
                    <div class="product-details-sizes">
                        <?php
                        $isBlanc = stripos($product['name'], 'blanc') !== false;
                        $blanc = $isBlanc ? $product : $sibling;
                        $noir  = $isBlanc ? $sibling : $product;
                        ?>
                        <a
                            class="size-button<?= $isBlanc ? ' active' : '' ?>"
                            href="/produto?id=<?= urlencode($blanc['id']) ?>"
                            style="text-decoration:none;"
                        ><?= htmlspecialchars(I18n::t('product.color.blanc')) ?></a>
                        <a
                            class="size-button<?= !$isBlanc ? ' active' : '' ?>"
                            href="/produto?id=<?= urlencode($noir['id']) ?>"
                            style="text-decoration:none;"
                        ><?= htmlspecialchars(I18n::t('product.color.noir')) ?></a>
                    </div>
                <?php endif; ?>

                <?php
                $classes  = ['btn', 'btn-style-outline', 'btn-variant-primary', 'btn-size-medium', 'default', 'btn-icon', 'qty-btn'];
                ?>

                <div class="product-details-actions">
                    <div class="quantity-selector">
                        <label><?= htmlspecialchars(I18n::t('product.quantity')) ?></label>
                        <div class="quantity-controls">
                            <button class="<?= implode(' ', $classes) ?>" onclick="updateQuantity('decrement')">
                                <?= \App\Helpers\IconHelper::minus(24, 'currentColor') ?>
                            </button>
                            <span class="qty-value" id="qty-value">1</span>
                            <button class="<?= implode(' ', $classes) ?>" onclick="updateQuantity('increment')">
                                <?= \App\Helpers\IconHelper::plus(24, 'currentColor') ?>
                            </button>
                        </div>
                    </div>

                    <button
                        class="btn btn-style-solid btn-variant-primary btn-size-extra-large add-to-cart-btn"
                        onclick="addToCart()"
                    ><?= htmlspecialchars(I18n::t('product.add_to_cart')) ?></button>
                </div>

                <div class="product-details-shipping">
                    <div class="shipping-item">
                        <img src="/assets/img/icons/icon-clock.svg" alt="Clock">
                        <span><?= htmlspecialchars(I18n::t('product.in_stock')) ?></span>
                    </div>
                    <div class="shipping-item">
                        <img src="/assets/img/icons/icon-truck.svg" alt="Truck">
                        <span><?= htmlspecialchars(I18n::t('product.shipping')) ?></span>
                    </div>
                </div>

                <hr class="divider">

                <div class="product-details-composition">
                    <h3><?= htmlspecialchars(I18n::t('product.composition')) ?></h3>
                    <div class="composition-icons">
                        <img src="/assets/img/icons/icon-wash.svg" alt="Wash">
                        <img src="/assets/img/icons/icon-bleach.svg" alt="Bleach">
                        <img src="/assets/img/icons/icon-dry.svg" alt="Dry">
                        <img src="/assets/img/icons/icon-iron.svg" alt="Iron">
                        <img src="/assets/img/icons/icon-flower.svg" alt="Flower">
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<script>
    (function () {
        const section = document.querySelector('.product-details-section');
        if (!section) return;

        const productId      = section.dataset.productId;
        const eventId        = section.dataset.eventId;
        const checkoutUrl    = section.dataset.checkoutUrl;
        const defaultVariant = section.dataset.defaultVariant;
        const modalTitle     = section.dataset.modalTitle || '';
        let selectedVariant  = defaultVariant;

        window.updateQuantity = function (action) {
            const qtyValue = document.getElementById('qty-value');
            let v = parseInt(qtyValue.textContent, 10);
            if (action === 'increment') v++;
            else if (action === 'decrement' && v > 1) v--;
            qtyValue.textContent = v;
        };

        window.sizeSelect = function (btn) {
            document.querySelectorAll('.size-button').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            selectedVariant = btn.dataset.variantId || defaultVariant;
        };

        window.addToCart = function () {
            const productVariantId = productId + '_' + selectedVariant;
            const qty = parseInt(document.getElementById('qty-value').textContent, 10) || 1;
            if (typeof openCustomModal === 'function') {
                openCustomModal(modalTitle, eventId, productVariantId, qty);
            } else {
                window.location.href = checkoutUrl + eventId + '?produto=' + productVariantId + '&qtd=' + qty;
            }
        };
    })();
</script>
