<?php
use App\Helpers\Component;

// Generate unique ID for this instance if not provided
$cartModalId = $id ?? uniqid('cart_modal_');
?>

<div class="cart-modal-backdrop" id="<?= $cartModalId?>">
    <div class="cart-modal-container">
        <!-- Header -->
        <div class="cart-modal-header">
            <h2 class="cart-modal-title">CARRINHO DE COMPRAS</h2>
            <button class="cart-modal-close" aria-label="Close Cart" data-cart-close>
                <?=\App\Helpers\IconHelper::close(16, 'var(--color-neutral-1100)') ?? '<img src="/assets/img/icons/icon-close.svg" alt="Close">'?>
            </button>
        </div>

        <hr class="cart-modal-divider">

        <!-- Main Content -->
        <div class="cart-modal-body">
            <!-- Left Side: Product List -->
            <div class="cart-modal-products">
                <a href="#" class="cart-modal-back" data-cart-close>
                    <?=\App\Helpers\IconHelper::keyboardReturn(16, 'var(--color-primary-400-main)') ?? '<img src="/assets/img/icons/icon-return.svg" alt="Return">'?>
                    <span>Retour</span>
                </a>

                <div id="cart-items-container">
                    <!-- Products will be injected here dynamically by Javascript -->
                </div>
            </div>

            <!-- Right Side: Summary -->
            <div class="cart-modal-summary">
                <div class="cart-summary-card">
                    <div class="cart-promo-container">
                        <?= Component::render('Button', [
    'class' => 'cart-promo-btn btn-block',
    'variant' => 'primary',
    'style' => 'outline',
    'size' => 'small',
    'label' => 'CODE PROMO'
])?>
                        <div class="cart-promo-input-wrapper hidden" id="cart-promo-input-wrapper">
                            <?= Component::render('Input', [
    'class' => 'cart-promo-input',
    'size' => 'Small',
    'placeholder' => 'CODE PROMO',
    'label' => false,
])?>
                        </div>
                    </div>
                    <div id="cart-summary-list">
                        <!-- Summary lines will be injected here dynamically by Javascript -->
                    </div>

                    <hr class="cart-modal-divider-sm">

                    <div class="cart-summary-fees">
                        <span class="fee-text">Custos de operação com impostos</span>
                        <span class="fee-price">€ 2,05</span>
                    </div>

                    <div class="cart-summary-total">
                        <span class="total-text">Total</span>
                        <span class="total-price" id="cart-total-price">€ 26,95</span>
                    </div>

                    <?= Component::render('Button', [
    'class' => 'cart-checkout-btn btn-block',
    'variant' => 'primary',
    'style' => 'solid',
    'size' => 'medium',
    'label' => 'CONTINUER'
])?>
                </div>
            </div>
        </div>
    </div>
</div>