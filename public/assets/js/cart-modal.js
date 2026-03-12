document.addEventListener('DOMContentLoaded', () => {
    // Modal elements
    const cartModalId = 'cart_modal_instance'; // Using a fixed ID for simplicity or expecting it in the DOM
    const cartModal = document.querySelector('.cart-modal-backdrop');

    // Close buttons
    const closeBtns = document.querySelectorAll('[data-cart-close], .cart-modal-back');

    if (!cartModal) return;

    // Helper for formatting prices
    const formatPrice = (price) => {
        return '€ ' + price.toFixed(2).replace('.', ',');
    };

    // State Key
    const CART_STORAGE_KEY = 'yanns_cart_items';

    // Load Items List
    let cartItems = [];
    try {
        const stored = localStorage.getItem(CART_STORAGE_KEY);
        if (stored) {
            cartItems = JSON.parse(stored);
            if (!Array.isArray(cartItems)) cartItems = [];
        }
    } catch (e) {
        cartItems = [];
    }

    // Save Items List
    const saveCart = () => {
        localStorage.setItem(CART_STORAGE_KEY, JSON.stringify(cartItems));
    };

    // Helper to get total qty
    const getTotalQty = () => {
        return cartItems.reduce((sum, item) => sum + item.qty, 0);
    };

    // Helper to get total price
    const getSubtotal = () => {
        return cartItems.reduce((sum, item) => sum + (item.price * item.qty), 0);
    };

    // Update the cart badges in the header and mobile menu
    const updateCartBadges = () => {
        const qty = getTotalQty();
        const badges = document.querySelectorAll('.cart-badge');
        badges.forEach(badge => {
            if (qty > 0) {
                badge.textContent = qty;
                badge.classList.remove('hidden');
            } else {
                badge.textContent = '0';
                badge.classList.add('hidden');
            }
        });

        // Sync main page quantity if it exists (ensuring it never reaches 0 on the product page)
        const pageQtyDisplay = document.getElementById('qty-value');
        if (pageQtyDisplay) {
            pageQtyDisplay.textContent = Math.max(1, qty === 0 ? 1 : parseInt(pageQtyDisplay.textContent) || 1);
        }
    };

    // A function to handle updates inside the modal
    const renderCart = () => {
        const itemsContainer = document.getElementById('cart-items-container');
        const summaryList = document.getElementById('cart-summary-list');
        const feesContainer = cartModal.querySelector('.cart-summary-fees');
        const checkoutBtn = cartModal.querySelector('.cart-checkout-btn');
        const promoContainer = cartModal.querySelector('.cart-promo-container');
        const cartTotalPriceDisplay = cartModal.querySelector('#cart-total-price');
        const feeDisplay = cartModal.querySelector('.fee-price');

        // Remove old empty message if any
        let emptyMsg = cartModal.querySelector('.cart-empty-message');
        if (emptyMsg) emptyMsg.remove();

        if (!itemsContainer || !summaryList) return;

        itemsContainer.innerHTML = '';
        summaryList.innerHTML = '';

        const totalQty = getTotalQty();

        if (totalQty === 0) {
            // Empty State
            if (feesContainer) feesContainer.style.display = 'none';
            if (checkoutBtn) checkoutBtn.style.display = 'none';
            if (promoContainer) promoContainer.style.display = 'none';
            if (cartTotalPriceDisplay) cartTotalPriceDisplay.textContent = formatPrice(0);
            if (feeDisplay) feeDisplay.textContent = formatPrice(0);

            // Reset external product page states
            const activeSizeButtons = document.querySelectorAll('.size-button.active');
            activeSizeButtons.forEach(btn => btn.classList.remove('active'));

            emptyMsg = document.createElement('div');
            emptyMsg.className = 'cart-empty-message';
            emptyMsg.style.padding = '24px 0';
            emptyMsg.style.display = 'flex';
            emptyMsg.style.flexDirection = 'column';
            emptyMsg.style.alignItems = 'flex-start';
            emptyMsg.style.gap = '24px';

            const emptyText = document.createElement('div');
            emptyText.textContent = 'Votre panier est vide.';
            emptyText.style.fontFamily = "'Inter', sans-serif";
            emptyText.style.fontSize = '16px';

            const keepShoppingBtn = document.createElement('a');
            keepShoppingBtn.href = '/collections';
            keepShoppingBtn.textContent = 'Continuer les achats';
            keepShoppingBtn.className = 'btn btn-style-solid btn-variant-primary btn-size-medium';
            keepShoppingBtn.style.alignSelf = 'stretch';
            keepShoppingBtn.style.marginTop = '16px';
            keepShoppingBtn.addEventListener('click', (e) => {
                if (window.location.pathname.includes('/collections')) {
                    e.preventDefault();
                    closeCartModal();
                }
            });

            emptyMsg.appendChild(emptyText);
            emptyMsg.appendChild(keepShoppingBtn);
            itemsContainer.appendChild(emptyMsg);

            // Placeholder Summary
            const placeholderSummary = document.createElement('div');
            placeholderSummary.className = 'cart-summary-items';
            placeholderSummary.style.display = 'flex';
            placeholderSummary.innerHTML = `
                <div class="cart-summary-item-desc">
                    <span class="desc-text">Aucun article sélectionné</span>
                    <span class="desc-price">${formatPrice(0)}</span>
                </div>
            `;
            summaryList.appendChild(placeholderSummary);
        } else {
            // Populated State
            if (feesContainer) feesContainer.style.display = 'flex';
            if (checkoutBtn) checkoutBtn.style.display = 'inline-flex';
            if (promoContainer) promoContainer.style.display = 'block';

            const tax = 2.05; // fixed mock tax for now
            const subtotal = getSubtotal();
            const finalTotal = subtotal + tax;

            if (cartTotalPriceDisplay) cartTotalPriceDisplay.textContent = formatPrice(finalTotal);
            if (feeDisplay) feeDisplay.textContent = formatPrice(tax);

            // Render Each Item
            cartItems.forEach((item, index) => {
                // Main Product List Item
                const productEl = document.createElement('div');
                productEl.className = 'cart-product-item';
                productEl.style.display = 'flex';
                productEl.innerHTML = `
                    <div class="cart-product-image">
                        <img src="/assets/img/collections/${item.image}" alt="${item.name}">
                    </div>
                    <div class="cart-product-info">
                        <h3 class="cart-product-title">${item.name} 
                            ${item.size ? `<span class="cart-product-size-label" style="font-weight: 400; font-size: 14px; color: var(--color-neutral-600); text-transform: none; display:block; margin-top:4px;">Taille: ${item.size}</span>` : ''}
                        </h3>
                        <p class="cart-product-price">${formatPrice(item.price)}</p>
                    </div>
                    <div class="cart-product-quantity">
                        <div class="quantity-controls">
                            <button class="btn btn-style-outline btn-variant-primary btn-size-small btn-icon cart-qty-minus" data-idx="${index}" aria-label="Decrease quantity">
                                ${'<img src="/assets/img/icons/minus.svg" alt="Minus">'}
                            </button>
                            <span class="cart-qty-value">${item.qty}</span>
                            <button class="btn btn-style-outline btn-variant-primary btn-size-small btn-icon cart-qty-plus" data-idx="${index}" aria-label="Increase quantity">
                                ${'<img src="/assets/img/icons/plus.svg" alt="Plus">'}
                            </button>
                        </div>
                    </div>
                `;
                itemsContainer.appendChild(productEl);

                // Summary List Item
                const summaryEl = document.createElement('div');
                summaryEl.className = 'cart-summary-items';
                summaryEl.style.display = 'flex';
                summaryEl.innerHTML = `
                    <div class="cart-summary-item-desc">
                        <span class="desc-text">${item.qty}x ${item.name} ${item.size ? `(Noir / ${item.size})` : ''}</span>
                        <span class="desc-price">${formatPrice(item.price * item.qty)}</span>
                    </div>
                    <div class="cart-summary-item-remove">
                        <button class="remove-btn" data-idx="${index}">Effacer</button>
                    </div>
                `;
                summaryList.appendChild(summaryEl);
            });

            // Attach dynamic events for the new buttons
            itemsContainer.querySelectorAll('.cart-qty-plus').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const idx = parseInt(btn.getAttribute('data-idx'));
                    cartItems[idx].qty++;
                    saveCart();
                    renderCart();
                    updateCartBadges();
                });
            });

            itemsContainer.querySelectorAll('.cart-qty-minus').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const idx = parseInt(btn.getAttribute('data-idx'));
                    if (cartItems[idx].qty > 1) {
                        cartItems[idx].qty--;
                    } else {
                        cartItems.splice(idx, 1);
                    }
                    saveCart();
                    renderCart();
                    updateCartBadges();
                });
            });

            summaryList.querySelectorAll('.remove-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const idx = parseInt(btn.getAttribute('data-idx'));
                    cartItems.splice(idx, 1);
                    saveCart();
                    renderCart();
                    updateCartBadges();
                });
            });
        }
    };

    // To open
    const openCartModal = (e, isAdding = false) => {
        if (e) e.preventDefault();

        if (isAdding) {
            // Explicitly adding item from product page
            let selectedSizeText = '';
            const sizeButtons = document.querySelectorAll('.size-button');
            if (sizeButtons.length > 0) {
                const selectedSize = document.querySelector('.size-button.active');
                if (selectedSize) {
                    selectedSizeText = selectedSize.textContent.trim();
                }
            }

            const productQtyDisplay = document.getElementById('qty-value');
            const productQty = productQtyDisplay ? (parseInt(productQtyDisplay.textContent) || 1) : 1;

            // Mock Product Data extraction (can be customized later to pull from DOM dataset attributes instead)
            const productNameEl = document.querySelector('.product-title');
            const productTitle = productNameEl ? productNameEl.textContent.trim() : 'CELLE/CELUI - T-SHIRT';

            const productPriceStr = document.querySelector('.product-price')?.textContent.replace(/[^0-9,]/g, '').replace(',', '.') || '24.90';
            const productPrice = parseFloat(productPriceStr);

            const itemId = productTitle.replace(/\s+/g, '-').toLowerCase();

            // Find existing matching item
            const existingIndex = cartItems.findIndex(item => item.id === itemId && item.size === selectedSizeText);

            // Extract the current image shown on the page (usually the selected color/variant)
            let productImageSrc = 'casquette-black.png'; // default fallback
            const mainSliderImage = document.querySelector('.main-slider .swiper-slide-active img');
            if (mainSliderImage) {
                // Try to grab just the filename, assuming images are in /assets/img/collections/
                const srcParts = mainSliderImage.src.split('/');
                productImageSrc = srcParts[srcParts.length - 1];
            } else {
                const firstProductImage = document.querySelector('.product-details-main-image img'); // fallback if no swiper active
                if (firstProductImage) {
                    const srcParts = firstProductImage.src.split('/');
                    productImageSrc = srcParts[srcParts.length - 1];
                }
            }


            if (existingIndex !== -1) {
                cartItems[existingIndex].qty += productQty;
                cartItems[existingIndex].image = productImageSrc; // update image if variant changed
            } else {
                cartItems.push({
                    id: itemId,
                    name: productTitle,
                    size: selectedSizeText,
                    price: productPrice,
                    qty: productQty,
                    image: productImageSrc
                });
            }

            saveCart();
        }

        renderCart();
        updateCartBadges();

        cartModal.classList.add('active');
        document.body.style.overflow = 'hidden'; // Prevent scrolling
    };

    // To close
    const closeCartModal = (e) => {
        if (e) e.preventDefault();
        cartModal.classList.remove('active');
        document.body.style.overflow = ''; // Restore scrolling
    };

    // Use event delegation for Add to Cart button since its disabled state might change dynamically
    document.body.addEventListener('click', (e) => {
        // If it's the Add to Cart button (id=btn-add-cart) or a child of it
        const addCartBtn = e.target.closest('#btn-add-cart');
        if (addCartBtn) {
            // Check if sizes exist and one is selected
            const sizeButtons = document.querySelectorAll('.size-button');
            if (sizeButtons.length > 0) {
                const selectedSize = document.querySelector('.size-button.active');
                if (!selectedSize) {
                    e.preventDefault();
                    // Alert the user using Toastify styled to match the site
                    Toastify({
                        text: "Veuillez choisir une taille avant d'ajouter au panier.",
                        duration: 3000,
                        gravity: "top", // top or bottom
                        position: "center", // left, center or right
                        stopOnFocus: true, // Prevents dismissing of toast on hover
                        style: {
                            background: "var(--color-neutral-1100)",
                            color: "var(--color-neutral-100)",
                            fontFamily: "'Inter', sans-serif",
                            fontWeight: "700",
                            borderRadius: "4px",
                            padding: "12px 24px",
                            boxShadow: "0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)"
                        }
                    }).showToast();
                    return; // Stop here, do not add to cart
                }
            }

            // Get qty from input
            const productQtyDisplay = document.getElementById('qty-value');
            const productQty = productQtyDisplay ? (parseInt(productQtyDisplay.textContent) || 1) : 1;
            updateCartBadges(productQty);
            openCartModal(e, true);
        }

        // Handle cart icon from header properly based on data attribute
        const cartIcon = e.target.closest('[data-cart-open]');
        if (cartIcon && !addCartBtn) {
            openCartModal(e, false);
        }
    });

    // Attach close events
    closeBtns.forEach(btn => {
        btn.addEventListener('click', closeCartModal);
    });

    // Close on backdrop click (if clicking outside the container)
    cartModal.addEventListener('click', (e) => {
        if (e.target === cartModal) {
            closeCartModal();
        }
    });

    // Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && cartModal.classList.contains('active')) {
            closeCartModal();
        }
    });

    // Promo Code Toggle
    const promoBtn = cartModal.querySelector('.cart-promo-btn');
    const promoInputWrapper = document.getElementById('cart-promo-input-wrapper');

    if (promoBtn && promoInputWrapper) {
        promoBtn.addEventListener('click', (e) => {
            e.preventDefault();
            promoBtn.classList.add('hidden');
            promoInputWrapper.classList.remove('hidden');

            const inputField = promoInputWrapper.querySelector('.input-field');
            if (inputField) {
                inputField.focus();
            }
        });

        // Optional: Revert to button if input loses focus and is empty
        const inputField = promoInputWrapper.querySelector('.input-field');
        if (inputField) {
            inputField.addEventListener('blur', () => {
                if (!inputField.value.trim()) {
                    promoInputWrapper.classList.add('hidden');
                    promoBtn.classList.remove('hidden');
                }
            });
        }
    }

    // --- On Load Restore ---
    const restoreCartState = () => {
        // Hydration logic is already handled at the top when reading into cartItems array
        renderCart();
        updateCartBadges();
    };

    restoreCartState();
});
