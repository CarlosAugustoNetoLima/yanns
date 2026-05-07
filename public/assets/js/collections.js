document.addEventListener('DOMContentLoaded', () => {
    const tabs = document.querySelectorAll('.collection-tab-item');
    const grid = document.getElementById('collections-grid');

    if (!grid) return;

    const messages = {
        loading: grid.dataset.msgLoading || 'Loading…',
        error:   grid.dataset.msgError   || 'Error loading products.',
        empty:   grid.dataset.msgEmpty   || 'No products available.',
        noImage: grid.dataset.msgNoImage || 'Image coming soon',
    };

    let cachedProducts = null;

    const fetchProducts = async () => {
        if (cachedProducts) return cachedProducts;
        const res = await fetch('/api/products');
        if (!res.ok) throw new Error('Failed to load products: ' + res.status);
        cachedProducts = await res.json();
        return cachedProducts;
    };

    const formatPrice = (price) => {
        const n = Number(price) || 0;
        return n.toFixed(2).replace('.', ',') + ' €';
    };

    const renderProducts = (products) => {
        grid.innerHTML = '';
        if (!products.length) {
            grid.innerHTML = '<div class="collections-loading" style="grid-column: 1 / -1; text-align: center; padding: 40px;">' + messages.empty + '</div>';
            return;
        }
        products.forEach(product => {
            const card = document.createElement('a');
            card.href = '/produto?id=' + encodeURIComponent(product.id);
            card.className = 'store-card';
            card.style.textDecoration = 'none';
            card.style.color = 'inherit';
            const imageHtml = product.image
                ? `<img src="${product.image}" alt="${product.name}">`
                : `<div class="store-card-no-image">${messages.noImage}</div>`;
            card.innerHTML = `
                <div class="store-card-image">
                    ${imageHtml}
                </div>
                <div class="store-card-content">
                    <div class="store-card-title">
                        <p>${product.name}</p>
                    </div>
                    <p class="store-card-price">${formatPrice(product.price)}</p>
                </div>
            `;
            grid.appendChild(card);
        });
    };

    const filterByCategory = (products, category) => {
        if (!category || category === 'ALL') return products;
        const cat = category.toLowerCase();
        return products.filter(p => (p.type || '').toLowerCase() === cat);
    };

    const loadCategory = async (category) => {
        grid.innerHTML = '<div class="collections-loading" style="grid-column: 1 / -1; text-align: center; padding: 40px; font-family: \'Inter\', sans-serif;">' + messages.loading + '</div>';
        try {
            const all = await fetchProducts();
            renderProducts(filterByCategory(all, category));
        } catch (err) {
            console.error(err);
            grid.innerHTML = '<div class="collections-loading" style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #c33;">' + messages.error + '</div>';
        }
    };

    tabs.forEach(tab => {
        tab.addEventListener('click', (e) => {
            e.preventDefault();
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            loadCategory(tab.textContent.trim());
        });
    });

    const initialActive = document.querySelector('.collection-tab-item.active');
    loadCategory(initialActive ? initialActive.textContent.trim() : 'ALL');
});
