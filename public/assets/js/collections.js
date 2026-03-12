document.addEventListener('DOMContentLoaded', () => {
    const tabs = document.querySelectorAll('.collection-tab-item');
    const grid = document.getElementById('collections-grid');

    if (!tabs.length || !grid) return;

    // Simulate an API response
    const mockApiFetch = async (category) => {
        // In a real scenario, this would be: await fetch(`/api/products?category=${encodeURIComponent(category)}`)
        return new Promise(resolve => {
            setTimeout(() => {
                // Different data for different categories to show it's dynamic
                const data = category === 'CELUI/CELLE' ? [
                    {
                        image: '/assets/img/collections/tshirt-black.png',
                        title: `${category} - T-SHIRT`,
                        price: '24,90 €'
                    },
                    {
                        image: '/assets/img/collections/tshirt-white.png',
                        title: `${category} - T-SHIRT (WHITE)`,
                        price: '24,90 €'
                    },
                    {
                        image: '/assets/img/collections/casquette-black.png',
                        title: `${category} - CASQUETTE`,
                        price: '24,90 €'
                    },
                    {
                        image: '/assets/img/collections/phone-case.png',
                        title: `${category} - COQUE`,
                        price: '24,90 €'
                    }
                ] : [
                    {
                        image: '/assets/img/collections/casquette-black.png',
                        title: `${category} - CASQUETTE EXCLUSIVITÉ`,
                        price: '29,90 €'
                    },
                    {
                        image: '/assets/img/collections/tshirt-black.png',
                        title: `${category} - T-SHIRT EXCLUSIF`,
                        price: '34,90 €'
                    }
                ];
                resolve(data);
            }, 300); // simulate network delay
        });
    };

    const renderProducts = (products) => {
        grid.innerHTML = '';
        products.forEach(product => {
            // Replicate the StoreCard HTML structure
            const card = document.createElement('a');
            card.href = '/produto?id=' + encodeURIComponent(product.title);
            card.className = 'store-card';
            card.style.textDecoration = 'none'; // Ensure no default link styles
            card.style.color = 'inherit';
            card.innerHTML = `
                <div class="store-card-image">
                    <img src="${product.image}" alt="${product.title}">
                </div>
                <div class="store-card-content">
                    <div class="store-card-title">
                        <p>${product.title}</p>
                    </div>
                    <p class="store-card-price">${product.price}</p>
                </div>
            `;
            grid.appendChild(card);
        });
    };

    const loadCategory = async (category) => {
        grid.innerHTML = '<div class="collections-loading" style="grid-column: 1 / -1; text-align: center; padding: 40px; font-family: \'Inter\', sans-serif;">Carregando produtos...</div>';
        const products = await mockApiFetch(category);
        renderProducts(products);
    };

    tabs.forEach(tab => {
        tab.addEventListener('click', (e) => {
            e.preventDefault();

            // Remove active class from all
            tabs.forEach(t => t.classList.remove('active'));
            // Add active to clicked
            tab.classList.add('active');

            const category = tab.textContent.trim();
            loadCategory(category);
        });
    });

    // Load initial category (the active one)
    const initialActive = document.querySelector('.collection-tab-item.active');
    if (initialActive) {
        loadCategory(initialActive.textContent.trim());
    }
});
