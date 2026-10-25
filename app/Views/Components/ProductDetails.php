<?php

use App\Helpers\Component;
use App\Helpers\I18n;

/** @var array|null $product */
/** @var array|null $sibling */
/** @var ?string $siblingKind  'color' (Blanc/Noir) ou 'age' (Enfant/Adulte) */
/** @var array|null $related   Outra versão do produto (ex.: Maillot Criança) */
/** @var int $eventId */
/** @var string $checkoutUrl */

$siblingKind = $siblingKind ?? null;
$related     = $related ?? null;

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

// Cor do produto (ex.: casquettes Blanc/Noir): mostrada como badge junto ao
// título, igual aos cards. Quando definida, a "variante" única é apenas a cor,
// então não exibimos seletor de variante — a variante padrão segue para o carrinho.
$colorKey   = $product['color'] ?? null;          // 'blanc' | 'noir' | null
$colorLabel = $colorKey ? I18n::t('product.color.' . $colorKey) : null;

// Traduz rótulos de variante não-numéricos (ex.: "Criança"/"Adulto" vindos do
// LeBillet) seguindo o idioma do site. Tamanhos (S/M/L/XL) passam inalterados.
$variantLabel = function (string $raw): string {
    $key = strtolower(trim(preg_replace('/[\x{00A0}\s]+/u', ' ', $raw)));
    $map = [
        'criança' => 'variant.child', 'crianca' => 'variant.child',
        'enfant'  => 'variant.child', 'kids'     => 'variant.child', 'child' => 'variant.child',
        'adulto'  => 'variant.adult', 'adulte'   => 'variant.adult', 'adult' => 'variant.adult',
        'masculino' => 'variant.male', 'masculin' => 'variant.male', 'homme' => 'variant.male',
        'men' => 'variant.male', 'male' => 'variant.male', 'garçon' => 'variant.boy', 'garcon' => 'variant.boy',
        'feminino' => 'variant.female', 'feminin' => 'variant.female', 'féminin' => 'variant.female',
        'femme' => 'variant.female', 'women' => 'variant.female', 'female' => 'variant.female', 'fille' => 'variant.girl',
    ];
    if (isset($map[$key])) {
        $translated = I18n::t($map[$key]);
        if ($translated !== $map[$key]) {
            return $translated;
        }
    }
    return $raw;
};
$defaultVariantId = $variants[0]['id'] ?? '1';

// Stock da variante padrão (usada no estado inicial)
$defaultStock = (int) ($variants[0]['stock'] ?? 10);

// ─── Dimensões das variantes: grupo (idade/género) × tamanho ───────────────
// Cada variante traz 'group' e/ou 'size' (parseado do LeBillet). Listamos os
// valores distintos para decidir que seletores mostrar e montamos um mapa
// "grupo|tamanho" → variante para o JS resolver a combinação escolhida.
// Tudo é dirigido pelos dados: assim que o LeBillet expuser idade + tamanho,
// os dois seletores aparecem automaticamente, sem mexer no código.
$variantGroups = [];
$variantSizes  = [];
foreach ($variants as $v) {
    if (!empty($v['group']) && !in_array($v['group'], $variantGroups, true)) {
        $variantGroups[] = $v['group'];
    }
    if (!empty($v['size']) && !in_array($v['size'], $variantSizes, true)) {
        $variantSizes[] = $v['size'];
    }
}
// Mantém a ORDEM exatamente como vem do LeBillet — sem reordenar no site.

// Grupo só vira seletor quando há escolha real (≥ 2 valores). O tamanho aparece
// mesmo com um único valor (ex.: Le Maillot Criança só tem XS) para o cliente ver.
// Produtos com cor (casquettes) não mostram seletor: a cor vira badge no título.
$showGroupSel = !$colorKey && count($variantGroups) >= 2;
$showSizeSel  = !$colorKey && count($variantSizes)  >= 1;
$hasGroupDim  = count($variantGroups) >= 1;
$hasSizeDim   = count($variantSizes)  >= 1;

// Mapa "grupo|tamanho" → variante (id, stock, preço) para o JS.
$variantMap = [];
foreach ($variants as $v) {
    $g  = $v['group'] ?? '';
    $s  = $v['size'] ?? '';
    $vp = $v['price'] ?? $product['price'];
    $variantMap[$g . '|' . $s] = [
        'id'         => $v['id'],
        'stock'      => (int) ($v['stock'] ?? 10),
        'priceLabel' => number_format((float) $vp, 2, ',', '.') . ' €',
    ];
}

// Todas as imagens do produto: mockup principal + fotos extras
$allImages = [];
if (!empty($product['image'])) {
    $allImages[] = $product['image'];
}
foreach ($product['images'] ?? [] as $extraImg) {
    $allImages[] = $extraImg;
}

?>

<section class="product-details-section"
         data-product-id="<?= htmlspecialchars($product['id']) ?>"
         data-event-id="<?= htmlspecialchars((string) $eventId) ?>"
         data-checkout-url="<?= htmlspecialchars($checkoutUrl) ?>"
         data-default-variant="<?= htmlspecialchars($defaultVariantId) ?>"
         data-default-stock="<?= $defaultStock ?>"
         data-modal-title="<?= htmlspecialchars(I18n::t('product.modal_title')) ?>">
    <div class="product-details-container">
        <a href="/collections" class="product-details-back">
            <?= \App\Helpers\IconHelper::keyboardReturn(16, 'var(--color-primary-400-main)') ?>
            <span><?= htmlspecialchars(I18n::t('product.return')) ?></span>
        </a>

        <div class="product-details-grid">

            <div class="product-details-gallery">
                <?php if (!empty($allImages)): ?>
                    <div class="product-details-thumbnails">
                        <?php foreach ($allImages as $idx => $imgSrc): ?>
                            <div class="thumbnail<?= $idx === 0 ? ' active' : '' ?>"
                                 data-thumb-src="<?= htmlspecialchars($imgSrc) ?>">
                                <img src="<?= htmlspecialchars($imgSrc) ?>"
                                     alt=""
                                     loading="<?= $idx === 0 ? 'eager' : 'lazy' ?>">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="product-details-main-image" data-zoom>
                        <img src="<?= htmlspecialchars($allImages[0]) ?>"
                             alt="<?= htmlspecialchars($product['name']) ?>"
                             id="product-main-img">
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
                    <?php if ($colorLabel !== null): ?>
                        <span class="product-details-color"><?= htmlspecialchars($colorLabel) ?></span>
                    <?php endif; ?>
                    <p class="product-price"><?= htmlspecialchars($priceLabel) ?></p>
                </div>

                <?php if ($sibling !== null && $siblingKind === 'color'): ?>
                    <?php
                    $isBlanc = stripos($product['name'], 'blanc') !== false;
                    $blanc = $isBlanc ? $product : $sibling;
                    $noir  = $isBlanc ? $sibling : $product;
                    ?>
                    <div class="product-details-selector">
                        <span class="selector-label"><?= htmlspecialchars(I18n::t('product.color')) ?></span>
                        <div class="product-details-sizes">
                            <a class="size-button<?= $isBlanc ? ' active' : '' ?>"
                               href="/produto?id=<?= urlencode($blanc['id']) ?>"
                               style="text-decoration:none;"><?= htmlspecialchars(I18n::t('product.color.blanc')) ?></a>
                            <a class="size-button<?= !$isBlanc ? ' active' : '' ?>"
                               href="/produto?id=<?= urlencode($noir['id']) ?>"
                               style="text-decoration:none;"><?= htmlspecialchars(I18n::t('product.color.noir')) ?></a>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($showGroupSel): ?>
                    <div class="product-details-selector">
                        <span class="selector-label"><?= htmlspecialchars(I18n::t('product.type')) ?></span>
                        <div class="product-details-sizes" data-dimension="group">
                            <?php foreach ($variantGroups as $i => $g): ?>
                                <div
                                    class="size-button<?= $i === 0 ? ' active' : '' ?>"
                                    data-group="<?= htmlspecialchars($g) ?>"
                                    onclick="dimensionSelect(this, 'group')"
                                >
                                    <?= htmlspecialchars($variantLabel($g)) ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($showSizeSel): ?>
                    <div class="product-details-selector">
                        <span class="selector-label"><?= htmlspecialchars(I18n::t('product.size')) ?></span>
                        <div class="product-details-sizes" data-dimension="size">
                            <?php foreach ($variantSizes as $i => $s): ?>
                                <div
                                    class="size-button<?= $i === 0 ? ' active' : '' ?>"
                                    data-size="<?= htmlspecialchars($s) ?>"
                                    onclick="dimensionSelect(this, 'size')"
                                >
                                    <?= htmlspecialchars($s) ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php elseif (!$showGroupSel && !$colorKey && count($variants) > 1): ?>
                    <?php /* Fallback: seletor simples de variantes (sem dimensões detectadas) */ ?>
                    <div class="product-details-sizes">
                        <?php foreach ($variants as $i => $variant):
                            $vStock = (int) ($variant['stock'] ?? 10);
                            $vPrice = $variant['price'] ?? $product['price'];
                            $vPriceLabel = number_format((float) $vPrice, 2, ',', '.') . ' €';
                        ?>
                            <div
                                class="size-button<?= $i === 0 ? ' active' : '' ?><?= $vStock === 0 ? ' sold-out' : '' ?>"
                                data-variant-id="<?= htmlspecialchars($variant['id']) ?>"
                                data-stock="<?= $vStock ?>"
                                data-price-label="<?= htmlspecialchars($vPriceLabel) ?>"
                                onclick="sizeSelect(this)"
                            >
                                <?= htmlspecialchars($variantLabel($variant['label'])) ?>
                            </div>
                        <?php endforeach; ?>
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
                        <?= $defaultStock === 0 ? 'disabled' : '' ?>
                    ><?= $defaultStock === 0 ? htmlspecialchars(I18n::t('product.out_of_stock')) : htmlspecialchars(I18n::t('product.add_to_cart')) ?></button>
                </div>

                <div class="product-details-shipping">
                    <div class="shipping-item">
                        <img src="/assets/img/icons/icon-clock.svg" alt="Clock">
                        <span id="stock-indicator"
                              class="stock-indicator <?= $defaultStock === 0 ? 'stock-out' : ($defaultStock < 10 ? 'stock-low' : 'stock-ok') ?>">
                            <?php if ($defaultStock === 0): ?>
                                <?= htmlspecialchars(I18n::t('product.out_of_stock')) ?>
                            <?php elseif ($defaultStock < 10): ?>
                                <?= htmlspecialchars(sprintf(I18n::t('product.stock_low'), $defaultStock)) ?>
                            <?php else: ?>
                                <?= htmlspecialchars(I18n::t('product.in_stock')) ?>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>

                <?php if ($related): ?>
                    <?php $relatedPrice = number_format((float) $related['price'], 2, ',', '.') . ' €'; ?>
                    <a class="product-crosslink" href="/produto?id=<?= urlencode($related['id']) ?>">
                        <?php if (!empty($related['image'])): ?>
                            <img class="product-crosslink-img" src="<?= htmlspecialchars($related['image']) ?>" alt="" loading="lazy">
                        <?php endif; ?>
                        <span class="product-crosslink-info">
                            <span class="product-crosslink-label"><?= htmlspecialchars(I18n::t('product.also_available')) ?></span>
                            <span class="product-crosslink-name"><?= htmlspecialchars($related['name']) ?> — <?= htmlspecialchars($relatedPrice) ?></span>
                        </span>
                        <svg class="product-crosslink-arrow" width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M9 6L15 12L9 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                <?php endif; ?>

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
        let currentStock     = parseInt(section.dataset.defaultStock ?? '10', 10);

        // ─── Validação de stock vs carrinho ─────────────────────────
        // Quantidade já no carrinho do site para um dado variante.
        function cartQtyFor(variantId) {
            if (!window.yannsCart || typeof window.yannsCart.items !== 'function') return 0;
            try {
                return window.yannsCart.items()
                    .filter(i => i.variantId === variantId)
                    .reduce((s, i) => s + (Number(i.qty) || 0), 0);
            } catch (_) { return 0; }
        }
        // Quanto ainda pode ser adicionado do variante atual (stock − carrinho).
        function remainingStock() {
            if (!(currentStock > 0)) return 0;
            const inCart = cartQtyFor(productId + '_' + selectedVariant);
            return Math.max(0, currentStock - inCart);
        }
        // Aviso (toast) quando o limite de stock é atingido.
        function notifyStockLimit(max) {
            const msg = '<?= addslashes(I18n::t('product.stock_limit')) ?>'.replace('%d', max);
            if (typeof window.Toastify === 'function') {
                window.Toastify({
                    text: msg,
                    duration: 3000,
                    gravity: 'top',
                    position: 'center',
                    style: { background: 'var(--color-primary-400-main, #930C2E)', fontFamily: "'Inter', sans-serif", borderRadius: '6px' },
                }).showToast();
            }
        }

        // ─── Mapa de variantes por dimensão (grupo × tamanho) ───────
        const variantMap = <?= json_encode($variantMap, JSON_UNESCAPED_UNICODE) ?>;
        const hasGroupDim = <?= $hasGroupDim ? 'true' : 'false' ?>;
        const hasSizeDim  = <?= $hasSizeDim ? 'true' : 'false' ?>;

        // Estado atual da seleção de dimensões
        let selectedGroup = <?= json_encode($variantGroups[0] ?? '') ?>;
        let selectedSize  = <?= json_encode($variantSizes[0] ?? '') ?>;

        /**
         * Dada a seleção atual de grupo + tamanho, encontra a variante correspondente
         * no variantMap e atualiza o estado (variant id, stock, preço).
         */
        function resolveVariant() {
            const key = (selectedGroup || '') + '|' + (selectedSize || '');
            const entry = variantMap[key];
            if (entry) {
                selectedVariant = entry.id;
                updateStockUI(entry.stock);
                const priceEl = document.querySelector('.product-price');
                if (priceEl && entry.priceLabel) {
                    priceEl.textContent = entry.priceLabel;
                }
            }
        }

        // ─── Atualiza UI de stock (indicador + botão + qty) ─────────
        function updateStockUI(stock) {
            currentStock = stock;
            const btn       = document.querySelector('.add-to-cart-btn');
            const indicator = document.getElementById('stock-indicator');
            const qtyVal    = document.getElementById('qty-value');

            if (btn) {
                btn.disabled    = stock === 0;
                btn.textContent = stock === 0
                    ? '<?= addslashes(I18n::t('product.out_of_stock')) ?>'
                    : '<?= addslashes(I18n::t('product.add_to_cart')) ?>';
            }
            if (indicator) {
                indicator.className = 'stock-indicator ' +
                    (stock === 0 ? 'stock-out' : stock < 10 ? 'stock-low' : 'stock-ok');
                if (stock === 0) {
                    indicator.textContent = '<?= addslashes(I18n::t('product.out_of_stock')) ?>';
                } else if (stock < 10) {
                    indicator.textContent = '<?= addslashes(I18n::t('product.stock_low_js')) ?>'.replace('%d', stock);
                } else {
                    indicator.textContent = '<?= addslashes(I18n::t('product.in_stock')) ?>';
                }
            }
            // Ajusta qty para não ultrapassar o stock
            if (qtyVal && stock > 0) {
                const v = parseInt(qtyVal.textContent, 10);
                if (v > stock) qtyVal.textContent = stock;
            }
        }

        window.updateQuantity = function (action) {
            const qtyValue = document.getElementById('qty-value');
            let v = parseInt(qtyValue.textContent, 10);
            if (action === 'increment') {
                // Limite = stock disponível menos o que já está no carrinho.
                const max = remainingStock();
                if (v >= max) { notifyStockLimit(currentStock); return; }
                v++;
            } else if (action === 'decrement' && v > 1) {
                v--;
            }
            qtyValue.textContent = v;
        };

        /**
         * Seletor de dimensão (grupo ou tamanho).
         * Ao clicar num botão, atualiza a seleção e resolve a variante.
         */
        window.dimensionSelect = function (btn, dimension) {
            // Desativa todos os botões da mesma dimensão
            const container = btn.closest('[data-dimension]');
            if (container) {
                container.querySelectorAll('.size-button').forEach(b => b.classList.remove('active'));
            }
            btn.classList.add('active');

            if (dimension === 'group') {
                selectedGroup = btn.dataset.group || '';
            } else if (dimension === 'size') {
                selectedSize = btn.dataset.size || '';
            }

            // Atualiza a disponibilidade dos botões do outro eixo
            updateButtonStates();
            resolveVariant();

            // Reset qty to 1 on dimension change
            const qtyVal = document.getElementById('qty-value');
            if (qtyVal) qtyVal.textContent = '1';
        };

        /**
         * Atualiza o estado visual (sold-out) dos botões de tamanho com base no grupo
         * selecionado, e vice-versa. Se uma combinação não existe no variantMap ou tem
         * stock 0, o botão fica com classe sold-out.
         */
        function updateButtonStates() {
            // Atualiza botões de tamanho com base no grupo selecionado
            const sizeContainer = section.querySelector('[data-dimension="size"]');
            if (sizeContainer) {
                sizeContainer.querySelectorAll('.size-button').forEach(btn => {
                    const s = btn.dataset.size || '';
                    const key = (selectedGroup || '') + '|' + s;
                    const entry = variantMap[key];
                    if (!entry || entry.stock === 0) {
                        btn.classList.add('sold-out');
                    } else {
                        btn.classList.remove('sold-out');
                    }
                });
            }

            // Atualiza botões de grupo com base no tamanho selecionado
            const groupContainer = section.querySelector('[data-dimension="group"]');
            if (groupContainer) {
                groupContainer.querySelectorAll('.size-button').forEach(btn => {
                    const g = btn.dataset.group || '';
                    const key = g + '|' + (selectedSize || '');
                    const entry = variantMap[key];
                    if (!entry || entry.stock === 0) {
                        btn.classList.add('sold-out');
                    } else {
                        btn.classList.remove('sold-out');
                    }
                });
            }
        }

        /**
         * Fallback: seletor simples (sem dimensões — apenas variant id direto).
         */
        window.sizeSelect = function (btn) {
            document.querySelectorAll('.size-button').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            selectedVariant = btn.dataset.variantId || defaultVariant;
            updateStockUI(parseInt(btn.dataset.stock ?? '10', 10));
            const priceEl = document.querySelector('.product-price');
            if (priceEl && btn.dataset.priceLabel) {
                priceEl.textContent = btn.dataset.priceLabel;
            }
            const qtyVal = document.getElementById('qty-value');
            if (qtyVal) qtyVal.textContent = '1';
        };

        // ─── Init: resolve a variante padrão e atualiza estados ─────
        if (hasGroupDim || hasSizeDim) {
            updateButtonStates();
            resolveVariant();
        }

        // ─── Troca de imagem ao clicar na thumbnail ─────────────────
        (function () {
            const mainImg = document.getElementById('product-main-img');
            if (!mainImg) return;
            document.querySelectorAll('.thumbnail[data-thumb-src]').forEach(function (thumb) {
                thumb.addEventListener('click', function () {
                    document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    mainImg.src = this.dataset.thumbSrc;
                    // reset zoom
                    mainImg.style.transform = 'scale(1)';
                    mainImg.style.transformOrigin = 'center center';
                });
            });
        })();

        // ─── Zoom acompanhando cursor na imagem principal ───────────
        (function () {
            const zoomBox = section.querySelector('[data-zoom]');
            if (!zoomBox) return;
            const img = zoomBox.querySelector('img');
            if (!img) return;

            const ZOOM_SCALE = 2.4;

            function onEnter() {
                img.style.transition = 'transform 0.18s ease';
                img.style.transform = 'scale(' + ZOOM_SCALE + ')';
                zoomBox.style.cursor = 'zoom-in';
            }
            function onLeave() {
                img.style.transition = 'transform 0.25s ease';
                img.style.transform = 'scale(1)';
                img.style.transformOrigin = 'center center';
            }
            function onMove(e) {
                const rect = zoomBox.getBoundingClientRect();
                const x = ((e.clientX - rect.left) / rect.width) * 100;
                const y = ((e.clientY - rect.top) / rect.height) * 100;
                img.style.transition = 'transform-origin 0s';
                img.style.transformOrigin = x + '% ' + y + '%';
            }

            zoomBox.addEventListener('mouseenter', onEnter);
            zoomBox.addEventListener('mouseleave', onLeave);
            zoomBox.addEventListener('mousemove', onMove);
        })();

        window.addToCart = function () {
            if (currentStock === 0) return; // segurança extra
            const productVariantId = productId + '_' + selectedVariant;
            let qty = parseInt(document.getElementById('qty-value').textContent, 10) || 1;

            // Não deixa o total no carrinho ultrapassar o stock.
            const max = remainingStock();
            if (max <= 0) { notifyStockLimit(currentStock); return; } // já tem o máximo
            if (qty > max) { qty = max; notifyStockLimit(currentStock); }
            const qtyEl = document.getElementById('qty-value');
            if (qtyEl) qtyEl.textContent = qty;

            // Coleta labels de dimensões selecionadas
            const activeGroupBtn = section.querySelector('[data-dimension="group"] .size-button.active');
            const activeSizeBtn  = section.querySelector('[data-dimension="size"] .size-button.active');
            const fallbackBtn    = document.querySelector('.size-button.active:not(a)');
            const sizeLabel = [
                activeGroupBtn ? activeGroupBtn.textContent.trim() : '',
                activeSizeBtn  ? activeSizeBtn.textContent.trim()  : '',
            ].filter(Boolean).join(' ') || (fallbackBtn ? fallbackBtn.textContent.trim() : '');

            const priceText = document.querySelector('.product-price')?.textContent || '0';
            const price = parseFloat(priceText.replace(/[^0-9,]/g, '').replace(',', '.')) || 0;

            const imgEl = document.querySelector('.product-details-main-image img');

            // Atualiza o carrinho do site silenciosamente (apenas para o badge do header)
            if (window.yannsCart && typeof window.yannsCart.add === 'function') {
                window.yannsCart.add({
                    id:        productId,
                    variantId: productVariantId,
                    eventId:   eventId,
                    name:      document.querySelector('.product-title')?.textContent.trim() || '',
                    size:      sizeLabel,
                    price:     price,
                    qty:       qty,
                    stock:     currentStock,
                    image:     imgEl ? imgEl.getAttribute('src') : '',
                });
            }

            // Vai direto para o checkout do LeBillet (tela de dados)
            if (typeof window.openCustomModal === 'function') {
                window.openCustomModal(modalTitle, eventId, productVariantId, qty);
            } else {
                window.location.href = checkoutUrl + eventId + '?produto=' + productVariantId + '&qtd=' + qty;
            }
        };

        // ─── Sincroniza o seletor de quantidade com o checkout ──────
        // O modal de checkout (iframe, mesma origem) expõe um <select> por
        // variante com a quantidade REAL no carrinho. Ao mudar a quantidade no
        // checkout ou fechar o modal, refletimos esse valor no seletor dos
        // detalhes — e vice-versa (o "Ajouter au panier" envia a qty escolhida).
        function readCheckoutQty() {
            try {
                const iframe = document.getElementById('modalCustomIframe');
                const doc = iframe && iframe.contentDocument;
                if (!doc) return null;
                const sel = doc.getElementById('product_' + productId + '_' + selectedVariant);
                if (!sel) return null;
                const v = parseInt(sel.value, 10);
                return Number.isFinite(v) ? v : null;
            } catch (_) { return null; }
        }
        function syncQtyFromCheckout() {
            const v = readCheckoutQty();
            if (v === null) return;                 // sem leitura → não mexe
            const qtyVal = document.getElementById('qty-value');
            if (!qtyVal) return;
            // qty 0 (removido) → volta ao default 1; senão reflete o checkout.
            qtyVal.textContent = v >= 1 ? String(v) : '1';
        }
        window.addEventListener('yanns:cartchanged', function () {
            // pequeno atraso para o iframe aplicar a alteração antes de lermos
            setTimeout(syncQtyFromCheckout, 350);
        });
    })();
</script>

