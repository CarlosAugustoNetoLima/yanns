<?php

use App\Helpers\Component;

// Get the product ID from URL query params (simulating database fetch)
$productId = $_GET['id'] ?? 'CELLE/CELUI - T-SHIRT';

// Map specific data if we recognize typical products, else fallbacks
$image = '/assets/img/collections/casquette-black.png';
$price = '24,90 €';

if (strpos(strtoupper($productId), 'T-SHIRT') !== false) {
    if (strpos(strtoupper($productId), 'WHITE') !== false) {
        $image = '/assets/img/collections/tshirt-white.png';
    }
    else {
        $image = '/assets/img/collections/tshirt-black.png';
    }
}
elseif (strpos(strtoupper($productId), 'COQUE') !== false) {
    $image = '/assets/img/collections/phone-case.png';
}

?>

<section class="product-details-section">
    <div class="product-details-container">
        <!-- Back button -->
        <a href="/collections" class="product-details-back">
            <?=\App\Helpers\IconHelper::keyboardReturn(16, 'var(--color-primary-400-main)')?>
            <span>Retour</span>
        </a>

        <!-- Main Layout -->
        <div class="product-details-grid">

            <!-- Left Side: Images -->
            <div class="product-details-gallery">
                <div class="product-details-thumbnails">
                    <div class="thumbnail active">
                        <img src="<?= htmlspecialchars($image)?>" alt="Thumbnail">
                    </div>
                    <div class="thumbnail">
                        <img src="<?= htmlspecialchars($image)?>" alt="Thumbnail">
                    </div>
                </div>
                <div class="product-details-main-image">
                    <img src="<?= htmlspecialchars($image)?>" alt="<?= htmlspecialchars($productId)?>">
                </div>
            </div>

            <!-- Right Side: Info -->
            <div class="product-details-info">

                <div class="product-details-header">
                    <h1 class="product-title">
                        <?= htmlspecialchars($productId)?>
                    </h1>
                    <p class="product-price">
                        <?= htmlspecialchars($price)?>
                    </p>
                </div>

                <!-- Sizes -->
                <div class="product-details-sizes">
                    <div class="size-button">XS</div>
                    <div class="size-button">S</div>
                    <div class="size-button">M</div>
                    <div class="size-button">L</div>
                    <div class="size-button">XL</div>
                    <div class="size-button">XXL</div>
                </div>

                <!-- Add to Cart Block -->
                <div class="product-details-actions">
                    <div class="quantity-selector">
                        <label>Quantité</label>
                        <div class="quantity-controls">
                            <?= Component::render('Button', [
    'id' => 'qty-minus',
    'class' => 'qty-btn',
    'variant' => 'primary',
    'style' => 'outline',
    'size' => 'medium',
    'icon' => 'minus',
    'ariaLabel' => 'Decrease quantity'
])?>
                            <span class="qty-value" id="qty-value">1</span>
                            <?= Component::render('Button', [
    'id' => 'qty-plus',
    'class' => 'qty-btn',
    'variant' => 'primary',
    'style' => 'outline',
    'size' => 'medium',
    'icon' => 'plus',
    'ariaLabel' => 'Increase quantity'
])?>
                        </div>
                    </div>

                    <?= Component::render('Button', [
    'id' => 'btn-add-cart',
    'class' => 'add-to-cart-btn',
    'variant' => 'primary',
    'style' => 'solid',
    'size' => 'extra large',
    'label' => 'AJOUTER AU PANIER'
])?>
                </div>

                <!-- Shipping Info -->
                <div class="product-details-shipping">
                    <div class="shipping-item">
                        <img src="/assets/img/icons/icon-clock.svg" alt="Clock">
                        <span>En stock, expédié sous 24H</span>
                    </div>
                    <div class="shipping-item">
                        <img src="/assets/img/icons/icon-truck.svg" alt="Truck">
                        <span>Livraison Standard par Mondial Relay : 4,90 €.</span>
                    </div>
                    <div class="shipping-item">
                        <img src="/assets/img/icons/icon-calendar.svg" alt="Calendar">
                        <span>Livraison estimée entre le 22/01 et le 28/01</span>
                    </div>
                </div>

                <!-- Divider -->
                <hr class="divider">

                <!-- Description -->
                <div class="product-details-description">
                    <h2>DESCRIPTION</h2>
                    <ul>
                        <li>T-Shirt noir, col rond et manches courtes</li>
                        <li>Collection "Celle/Celui" - modèle enfant</li>
                        <li>Imprimé "Yanns" à l'avant et "Celle/Celui qu'il te faut" à l'arrière du t-shirt</li>
                        <li>100% coton bio 155gr</li>
                        <li>Ce produit taille normalement, nous vous conseillons de prendre votre taille habituelle</li>
                    </ul>
                </div>

                <!-- Composition & Washing -->
                <div class="product-details-composition">
                    <h3>Composition et lavage:</h3>
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