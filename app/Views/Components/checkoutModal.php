<?php
use App\Helpers\Component;

$checkoutUrl = $checkoutUrl ?? 'https://checkout.lebillet.eu/';
?>

<div class="cart-modal-backdrop" id="checkoutM">
<div id="modalCustom" class="cart-modal-container">

    <div class="modal-custom-overlay" onclick="closeCustomModal()"></div>

    <div class="modal-custom-dialog">

        <div class="modal-custom-header">
            <span id="modalCustomTitle" class="modal-custom-title"></span>

            <button class="modal-custom-close" onclick="closeCustomModal()">
                &times;
            </button>
        </div>

        <div class="modal-custom-body cart-iframe-wrapper" id="modalCustomBody">
            <div class="cart-iframe-loader" id="modalCustomLoader" aria-hidden="true">
                <span class="cart-iframe-spinner"></span>
            </div>
            <iframe
                id="modalCustomIframe"
                src="about:blank"
                data-base-proxy="/checkout-proxy.php"
                title="Checkout"
                allow="payment"
                frameborder="0"
                style="opacity:0;transition:opacity 0.25s ease;"
            ></iframe>
        </div>

    </div>

</div>
</div>

<script>
(function () {
    let _loadedEventId  = '';
    let _loadedProductId = '';

    function showLoader() {
        const loader = document.getElementById('modalCustomLoader');
        const iframe = document.getElementById('modalCustomIframe');
        if (loader) loader.style.display = 'flex';
        if (iframe) iframe.style.opacity = '0';
    }

    function hideLoader() {
        const loader = document.getElementById('modalCustomLoader');
        const iframe = document.getElementById('modalCustomIframe');
        if (loader) loader.style.display = 'none';
        if (iframe) iframe.style.opacity = '1';
    }

    /**
     * Abre o modal de checkout.
     *
     * Forma nova (recomendada):
     *   openCustomModal(title, eventId, productId, qty)
     *   ex: openCustomModal('Merch', '1816', '10944_133', 2)
     *
     * Forma antiga (retrocompat): se o segundo argumento for uma URL completa
     * (http:// ou https://), apenas seta iframe.src para a URL — não há filtro
     * nem persistência.
     */
    window.openCustomModal = function (title, eventIdOrUrl, productId, qty) {
        const modal     = document.getElementById('modalCustom');
        const modalAll  = document.getElementById('checkoutM');
        const titleEl   = document.getElementById('modalCustomTitle');
        const iframe    = document.getElementById('modalCustomIframe');
        if (!modal || !iframe) return;

        if (titleEl) titleEl.innerText = title || '';

        const isLegacyUrl = typeof eventIdOrUrl === 'string'
            && /^https?:\/\//i.test(eventIdOrUrl);

        if (isLegacyUrl) {
            // Modo legado — comportamento antigo (sem proxy/filter)
            showLoader();
            iframe.onload = hideLoader;
            iframe.src = eventIdOrUrl;
            _loadedEventId   = '';
            _loadedProductId = '';
        } else {
            const eventId = String(eventIdOrUrl || '');
            const proxyBase = iframe.dataset.baseProxy || '/checkout-proxy.php';

            if (!eventId) {
                // Sem evento — não tenta carregar
                showLoader();
            } else if (
                _loadedEventId === eventId
                && iframe.src !== 'about:blank'
                && iframe.contentWindow
            ) {
                // Mesma sessão: só atualiza filtro via postMessage (preserva carrinho)
                iframe.contentWindow.postMessage(
                    {
                        type: 'yannsUpdateFilter',
                        productId: productId || '',
                        qty: typeof qty === 'number' ? qty : parseInt(qty, 10) || 0
                    },
                    window.location.origin
                );
                _loadedProductId = productId || '';
                hideLoader();
            } else {
                // Primeira carga ou evento diferente
                showLoader();
                iframe.onload = hideLoader;
                let url = proxyBase + '?event_id=' + encodeURIComponent(eventId);
                if (productId) url += '&product_id=' + encodeURIComponent(productId);
                if (qty && parseInt(qty, 10) > 0) url += '&qty=' + parseInt(qty, 10);
                iframe.src = url;
                _loadedEventId   = eventId;
                _loadedProductId = productId || '';
            }
        }

        modal.classList.add('active');
        if (modalAll) modalAll.classList.add('active');
        document.body.style.overflow = 'hidden';
    };

    window.closeCustomModal = function () {
        const modal    = document.getElementById('modalCustom');
        const modalAll = document.getElementById('checkoutM');
        if (modal) modal.classList.remove('active');
        if (modalAll) modalAll.classList.remove('active');
        document.body.style.overflow = '';
        // NÃO zera iframe.src — preserva sessão/carrinho da lebillet
    };

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') window.closeCustomModal();
    });

    // Recebe contagem do carrinho (futuro: badge no header)
    window.addEventListener('message', function (e) {
        if (!e.data || e.data.type !== 'yannsCartCount') return;
        // Hook para futuro: atualizar badge global
        if (typeof window.onYannsCartCount === 'function') {
            try { window.onYannsCartCount(e.data.count); } catch (_) {}
        }
    });
})();
</script>
