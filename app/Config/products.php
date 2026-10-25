<?php

return [
    // ─── Le Maillot (COLEÇÃO DESCONTINUADA — comentado para reutilizar depois) ──
    // O LeBillet expõe o Maillot como DOIS produtos separados, cada um vira um
    // card próprio na vitrine:
    //   12616 = Adulto (S/M/L/XL)  → SÓ fotos reais (lifestyle), sem mockup
    //   12615 = Criança (XS)       → SÓ o mockup, sem foto real
    // 'age_sibling' liga os dois: na página de um, um box "Également disponible"
    // leva ao outro (quem procura a versão criança/adulto encontra o caminho).
    // '12616' => [
    //     'image'      => '/assets/img/yanns/gallery/10.webp',
    //     'images'     => [
    //         '/assets/img/yanns/gallery/1.webp',
    //         '/assets/img/yanns/gallery/2.webp',
    //         '/assets/img/yanns/gallery/3.webp',
    //         '/assets/img/yanns/gallery/4.webp',
    //         '/assets/img/yanns/gallery/5.webp',
    //         '/assets/img/yanns/gallery/6.webp',
    //         '/assets/img/yanns/gallery/7.webp',
    //         '/assets/img/yanns/gallery/8.webp',
    //         '/assets/img/yanns/gallery/9.webp',
    //         '/assets/img/yanns/gallery/11.webp',
    //         '/assets/img/yanns/gallery/12.webp',
    //         '/assets/img/yanns/gallery/13.webp',
    //         '/assets/img/yanns/gallery/14.webp',
    //         '/assets/img/yanns/gallery/15.webp',
    //         '/assets/img/yanns/gallery/16.webp',
    //         '/assets/img/yanns/gallery/17.webp',
    //         '/assets/img/yanns/gallery/18.webp',
    //         '/assets/img/yanns/gallery/19.webp',
    //         '/assets/img/yanns/gallery/20.webp',
    //         '/assets/img/yanns/gallery/21.webp',
    //         '/assets/img/yanns/gallery/22.webp',
    //         '/assets/img/yanns/gallery/23.webp',
    //     ],
    //     'type'        => 'shirt',
    //     'age_sibling' => '12615',
    //     'sort_order'  => 1,
    // ],
    // '12615' => [
    //     'image'       => '/assets/img/collections/produtos/Le maillot.webp',
    //     'type'        => 'shirt',
    //     'age_sibling' => '12616',
    //     'sort_order'  => 1.5, // logo após o Maillot Adulto (sort 1), antes dos demais (2+)
    // ],
    '10944' => [
        'image'         => '/assets/img/collections/produtos/SALLE GOSSE - CELLE QU\'IL TE FAUT/BLANC/CELLE QU\'IL TE FAUT.webp',
        'type'          => 'shirt',
        'color_sibling' => '12277',
        'sort_order'    => 2,
    ],
    '12277' => [
        'image'         => '/assets/img/collections/produtos/SALLE GOSSE - CELLE QU\'IL TE FAUT/NOIR/CELLE QU\'IL TE FAUT.webp',
        'type'          => 'shirt',
        'color_sibling' => '10944',
        'sort_order'    => 3,
    ],
    '10945' => [
        'image'         => '/assets/img/collections/produtos/SALLE GOSSE - CELLE QU\'IL TE FAUT/BLANC/SALE GOSSE 2.webp',
        'type'          => 'shirt',
        'color_sibling' => '12282',
        'sort_order'    => 4,
    ],
    '12282' => [
        'image'         => '/assets/img/collections/produtos/SALLE GOSSE - CELLE QU\'IL TE FAUT/NOIR/SALE GOSSE 2.webp',
        'type'          => 'shirt',
        'color_sibling' => '10945',
        'sort_order'    => 5,
    ],
    '12279' => [
        'image'         => '/assets/img/collections/produtos/SALLE GOSSE - CELLE QU\'IL TE FAUT/BLANC/SALE GOSSE 1.webp',
        'type'          => 'shirt',
        'color_sibling' => '12280',
        'sort_order'    => 6,
    ],
    '12280' => [
        'image'         => '/assets/img/collections/produtos/SALLE GOSSE - CELLE QU\'IL TE FAUT/NOIR/SALE GOSSE 1.webp',
        'type'          => 'shirt',
        'color_sibling' => '12279',
        'sort_order'    => 7,
    ],
    '10947' => [
        'image'      => '/assets/img/collections/choco coco/NZR_3430.webp',
        'images'     => [
            '/assets/img/collections/produtos/COCO CHOCO/COCO CHOCO.webp',
            '/assets/img/yanns/gallery/32.webp',
            '/assets/img/yanns/gallery/33.webp',
            '/assets/img/yanns/gallery/35.webp',
            '/assets/img/collections/choco coco/1.webp',
            '/assets/img/collections/choco coco/2.webp',
            '/assets/img/collections/choco coco/3.webp',
            '/assets/img/collections/choco coco/4.webp',
            '/assets/img/collections/choco coco/5.webp',
        ],
        'type'       => 'shirt',
        'sort_order' => 8,
    ],
    '10948' => [
        'image'      => '/assets/img/yanns/gallery/26.webp',
        'images'     => [
            '/assets/img/collections/produtos/YANNS MIROIR/YANNS MIROIR MOCKUP.webp',
        ],
        'type'       => 'shirt',
        'sort_order' => 9,
    ],
    '10949' => [
        'image'      => '/assets/img/yanns/gallery/37.webp',
        'images'     => [
            '/assets/img/collections/produtos/t-shirt-clic-clic-pan-pan-blanc-front-14.webp',
            '/assets/img/yanns/gallery/24.webp',
            '/assets/img/yanns/gallery/31.webp',
            '/assets/img/yanns/gallery/36.webp',
            '/assets/img/yanns/gallery/38.webp',
            '/assets/img/yanns/gallery/40.webp',
        ],
        'type'       => 'shirt',
        'sort_order' => 10,
    ],
    '10950' => [
        'image'      => '/assets/img/collections/toke toke/NZR_3449.webp',
        'images'     => [
            '/assets/img/collections/produtos/toketoke.webp',
            '/assets/img/yanns/gallery/25.webp',
            '/assets/img/yanns/gallery/27.webp',
            '/assets/img/yanns/gallery/28.webp',
            '/assets/img/yanns/gallery/29.webp',
            '/assets/img/yanns/gallery/39.webp',
            '/assets/img/collections/toke toke/NZR_3470.webp',
        ],
        'type'       => 'shirt',
        'sort_order' => 11,
    ],
    '10951' => [
        'image'      => '/assets/img/collections/produtos/CASQUETTE/CASQUETE BLANCHE.webp',
        'type'       => 'casquette',
        'color'      => 'blanc',
        'sort_order' => 12,
    ],
    '10952' => [
        'image'      => '/assets/img/yanns/gallery/30.webp',
        'images'     => [
            '/assets/img/collections/produtos/CASQUETTE/CASQUETTE NOIR.webp',
        ],
        'type'       => 'casquette',
        'color'      => 'noir',
        'sort_order' => 13,
    ],
    '10953' => [
        'image'      => '/assets/img/collections/produtos/pistolet.webp',
        'type'       => 'accessory',
        'sort_order' => 14,
    ],
    '10954' => [
        'image'      => '/assets/img/collections/produtos/Disque.webp',
        'type'       => 'accessory',
        'sort_order' => 15,
    ],
    '10946' => [
        'image'      => '/assets/img/yanns/gallery/34.webp', // Je t'aime — foto lifestyle (única disponível)
        'type'       => 'shirt',
        'sort_order' => 16,
    ],
];
