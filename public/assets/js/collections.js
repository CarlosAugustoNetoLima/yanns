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
                const data = category === 'ALL' ? [
                    {
                        image: '/assets/img/collections/produtos/T-shirt – Celle qu’il te faut.png',
                        title: `T-shirt – Celle qu’il te faut`,
                        price: '25,00 €',
                        product: '10944',
                        type: 'shirt'
                    },
                    {
                        image: '/assets/img/collections/produtos/T-shirt – Sale gosse (Fille).png',
                        title: `T-shirt – Sale gosse (Fille)`,
                        price: '25,00 €',
                        product: '10945',
                        type: 'shirt'
                    },
                    {
                        image: '/assets/img/collections/produtos/T-shirt – Celle qu’il te faut.png',
                        title: `T-shirt – Je t’aime`,
                        price: '20,00 €',
                        product: '10946',
                        type: 'shirt'
                    },
                    {
                        image: '/assets/img/collections/produtos/T-shirt – Coco choco.png',
                        title: `T-shirt – Coco choco`,
                        price: '25,00 €',
                        product: '10947',
                        type: 'shirt'
                    },
                    {
                        image: '/assets/img/collections/produtos/T-shirt – Logo Yanns miroir.png',
                        title: `T-shirt – Logo Yanns miroir`,
                        price: '20,00 €',
                        product: '10948',
                        type: 'shirt'
                    },
                    {
                        image: '/assets/img/collections/produtos/T-shirt – Celle qu’il te faut.png',
                        title: `T-shirt – Clic clic pan pan`,
                        price: '20,00 €',
                        product: '10949',
                        type: 'shirt'
                    },
                     {
                        image: '/assets/img/collections/produtos/toketoke.png',
                        title: `T shirt - Toké Toké`,
                        price: '30,00 €',
                        product: '10950',
                        type: 'shirt'
                    },
                     {
                        image: '/assets/img/collections/produtos/CASQUETE BLANCHE.png',
                        title: `Casquette Blanche Yanns (Édition 1998)`,
                        price: '20,00 €',
                        product: '10951',
                        type: 'Casquette'
                    },
                     {
                        image: '/assets/img/collections/produtos/CASQUETTE NOIR.png',
                        title: `Casquette Noire Yanns (Édition 1998)`,
                        price: '20,00 €',
                        product: '10952',
                        type: 'Casquette'
                    },
                     {
                        image: '/assets/img/collections/produtos/pistolet.png',
                        title: `Porte-clé Yanns pistolet clic clic pan pan`,
                        price: '10,00 €',
                        product: '10953',
                        type: 'all'
                    },
                    {
                        image: '/assets/img/collections/produtos/Disque.png',
                        title: `Porte-clé Disque`,
                        price: '10,00 €',
                        product: '10954',
                        type: 'all'
                    }
                ] : [
                    {
                        image: '/assets/img/collections/casquette-black.png',
                        title: `T-shirt – Logo Yanns miroir`,
                        price: '29,90 €',
                        product: '10944',
                        type: 'all'
                    },
                    {
                        image: '/assets/img/collections/tshirt-black.png',
                        title: `T-shirt – Clic clic pan pan`,
                        price: '34,90 €',
                        product: '10944',
                        type: 'all'
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
            card.href = '/produto?id=' + encodeURIComponent(product.title) + '&image=' + encodeURIComponent(product.image) + '&price=' +  encodeURIComponent(product.price) + '&idP=' +  encodeURIComponent(product.product) + '&type=' +  encodeURIComponent(product.type);
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
            card.addEventListener('click', function () {
                abrirModal(product.product);
            });

            grid.appendChild(card);
        });
    };

    function abrirModal(product){
        console.log(product);
       // openCustomModal('Merchandising Yanns', 'https://checkout.lebillet.eu/1816?produto='+product)
    }

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
