<?php

use App\Helpers\Component;

// Get the product ID from URL query params (simulating database fetch)
$productId = $_GET['id'] ?? 'CELLE/CELUI - T-SHIRT';
$id = $_GET['idP'] ?? '10944';
$type = $_GET['type'] ?? 'all';

// Map specific data if we recognize typical products, else fallbacks
$image = $_GET['image'] ?? '/assets/img/collections/tshirt-black.png';
$price = $_GET['price'] ?? '00,00 €';

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
                    <!-- <div class="thumbnail">
                        <img src="<?= htmlspecialchars($image)?>" alt="Thumbnail">
                    </div> -->
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
                <?php if($type == 'shirt'){ ?>
                    <div class="product-details-sizes">
                        <div class="size-button" id="type_s" onclick="sizeSelect('type_s')">S</div>
                        <div class="size-button" id="type_m" onclick="sizeSelect('type_m')">M</div>
                        <div class="size-button" id="type_l" onclick="sizeSelect('type_l')">L</div>
                        <div class="size-button" id="type_xl" onclick="sizeSelect('type_xl')">XL</div>
                    </div>
                <?php } ?>

                <?php if($type == 'Casquette'){ ?>
                    <div class="product-details-sizes">
                        <div class="size-button" id="type_blanc" onclick="sizeSelect('type_blanc')">Blanc</div>
                        <div class="size-button" id="type_noir" onclick="sizeSelect('type_noir')">Noir</div>
                    </div>
                <?php } ?>

                <?php 
                    $classes = [
                        'btn',
                        "btn-style-outline",
                        "btn-variant-primary",
                        "btn-size-medium",
                        'default',
                        'btn-icon',
                        "qty-btn"
                    ];

                    $classes2 = [
                        'btn',
                        "btn-style-solid",
                        "btn-variant-primary",
                        "btn-size-extra large",
                        'default',
                        "btn-add-cart"
                    ];

                    $icon = 'minus';
                    $icon2 = 'plus';
                ?>

                <!-- Add to Cart Block -->
                <div class="product-details-actions">
                    <div class="quantity-selector">
                        <label>Quantité</label>
                        <div class="quantity-controls">
                            <button class="<?= implode(' ', array_filter($classes))?>" onclick="updateQuantity('decrement')">
                                <?php if ($icon && method_exists('\App\Helpers\IconHelper', $icon)){ ?>
                                    <?=\App\Helpers\IconHelper::$icon(24, 'currentColor')?>
                                <?php } ?>
                            </button>
                            <span class="qty-value" id="qty-value">1</span>
                            <button class="<?= implode(' ', array_filter($classes))?>" onclick="updateQuantity('increment')">
                                <?php if ($icon2 && method_exists('\App\Helpers\IconHelper', $icon2)){ ?>
                                    <?=\App\Helpers\IconHelper::$icon2(24, 'currentColor')?>
                                <?php } ?>
                            </button>
                        </div>
                    </div>

                     <button class="btn btn-style-solid btn-variant-primary btn-size-extra-large add-to-cart-btn" onclick="abrirModal('<?= htmlspecialchars($id)?>')">
                               AJOUTER AU PANIER
                            </button>
                </div>

                <!-- Shipping Info -->
                <div class="product-details-shipping">
                    <div class="shipping-item">
                        <img src="/assets/img/icons/icon-clock.svg" alt="Clock">
                        <span>En stock</span>
                    </div>
                    <div class="shipping-item">
                        <img src="/assets/img/icons/icon-truck.svg" alt="Truck">
                        <span>Livraison Standard par Mondial Relay : 10,00 €.</span>
                    </div>
               <!--     <div class="shipping-item">
                        <img src="/assets/img/icons/icon-calendar.svg" alt="Calendar">
                        <span>Livraison estimée entre le 22/01 et le 28/01</span>
                    </div>
                </div>
				-->

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

<script>
    var typeM = '';
    function updateQuantity(value) {
        const qtyValue = document.getElementById('qty-value');
        let currentValue = parseInt(qtyValue.textContent);

        if (value === 'increment') {
            currentValue++;
        } else if (value === 'decrement' && currentValue > 1) {
            currentValue--;
        }

        qtyValue.textContent = currentValue;
    }

    function sizeSelect(type){
       const btnType = document.getElementById(type);
       document.querySelectorAll('.size-button').forEach(btn => btn.classList.remove('active'));
       btnType.classList.add('active');
       typeM = type.split('_')[1];
    }

    function abrirModal(id){
        const qtyValue = document.getElementById('qty-value');
        let currentValue = parseInt(qtyValue.textContent);
    
        var typeP = '1';
        if(typeM == 's'){
             typeP = '132';
        }
        if(typeM == 'm'){
            typeP = '133';
        }

        if(typeM == 'l'){
            typeP = '134';
        }

        if(typeM == 'xl'){
            typeP = '135';
        }

         if(typeM == 'blanc'){
            typeP = '163';
        }

         if(typeM == 'noir'){
            typeP = '164';
        }
        openCustomModal('Merchandising Yanns', 'https://checkout.lebillet.eu/1816?produto='+id+'_' + typeP+'&qtd='+currentValue);
    }
</script>