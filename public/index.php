<?php
// Entry point
use App\Helpers\Component;
use App\Helpers\IconHelper;

// Simple Autoloader
spl_autoload_register(function ($class) {
    // Convert namespace to full file path
    // App\Config\Database -> app/Config/Database.php
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

/**
 * Cache-busting: anexa ?v=<filemtime> a um asset local, para que mudanças
 * em CSS/JS apareçam de imediato após o deploy mesmo com cache do navegador.
 */
$asset = function (string $path): string {
    $full = __DIR__ . $path;
    $v = is_file($full) ? filemtime($full) : null;
    return $v ? $path . '?v=' . $v : $path;
};

// I18n bootstrap. Se houve troca via ?lang=, redirecciona para a mesma URL sem o param.
$switchedLang = \App\Helpers\I18n::init();
if ($switchedLang !== null) {
    $cleanQuery = $_GET;
    unset($cleanQuery['lang']);
    $cleanPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
    $redirect  = $cleanPath . (empty($cleanQuery) ? '' : '?' . http_build_query($cleanQuery));
    header('Location: ' . $redirect, true, 302);
    exit;
}

// Basic Routing (very simple for now)
$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);

// Mapping paths to page ids
$routes = [
    '/' => 'home',
    '/index.php' => 'home',
    '/musique' => 'musique',
    '/dates' => 'dates',
    '/collections' => 'collections',
    '/biographie' => 'biographie',
    '/produto' => 'product_details'
];

// get events
$eventsArray = [
    (object) [
        'id' => 6,
        // 'name' => 'YANNS',
        'date_start' => '2026-08-21 20:00:00',
        'date_opening' => '2026-08-21 19:00:00',
        'city' => (object) ['name' => 'MONT-DE-MARSAN'],
        'country' => (object) ['name' => 'France'],
        'place' => (object) ['name' => 'Les Arènes (avec Kenn\'V)'],
        'ticketUrl' => 'http://billetterie.montdemarsan-tourisme.fr/concert/concerts-du-21-aout/soiree-concerts-du-21-aout-2026'
    ],
    (object) [
        'id' => 17,
        // 'name' => 'YANNS',
        'date_start' => '2026-08-22 20:00:00',
        'date_opening' => '2026-08-22 19:00:00',
        'city' => (object) ['name' => 'SAINT-MICHEL-EN-THIÉRACHE'],
        'country' => (object) ['name' => 'France'],
        'place' => (object) ['name' => 'À définir'],
        'ticketUrl' => '',
        'isFree' => true
    ],
    (object) [
        'id' => 7,
        // 'name' => 'YANNS',
        'date_start' => '2026-09-11 20:30:00',
        'date_opening' => '2026-09-11 19:30:00',
        'city' => (object) ['name' => 'BOURGES'],
        'country' => (object) ['name' => 'France'],
        'place' => (object) ['name' => 'Palais d\'Auron'],
        'ticketUrl' => 'https://my.weezevent.com/yanns-phenomenal-retour'
    ],
    (object) [
        'id' => 8,
        // 'name' => 'YANNS',
        'date_start' => '2026-09-12 20:00:00',
        'date_opening' => '2026-09-12 19:00:00',
        'city' => (object) ['name' => 'CALUIRE-ET-CUIRE (LYON)'],
        'country' => (object) ['name' => 'France'],
        'place' => (object) ['name' => 'Radiant-Bellevue'],
        'ticketUrl' => 'https://billetterie.radiant-bellevue.fr/fr/product/1205/radiant_bellevue/yanns'
    ],
    (object) [
        'id' => 9,
        // 'name' => 'YANNS',
        'date_start' => '2026-09-13 17:00:00',
        'date_opening' => '2026-09-13 16:00:00',
        'city' => (object) ['name' => 'REIMS'],
        'country' => (object) ['name' => 'France'],
        'place' => (object) ['name' => 'LE K'],
        'ticketUrl' => 'https://my.weezevent.com/yanns-phenomenal-retour-2'
    ],
    (object) [
        'id' => 10,
        // 'name' => 'YANNS',
        'date_start' => '2026-10-03 20:30:00',
        'date_opening' => '2026-10-03 19:30:00',
        'city' => (object) ['name' => 'ANDRIMONT'],
        'country' => (object) ['name' => 'Belgique'],
        'place' => (object) ['name' => 'Espace 58'],
        'ticketUrl' => 'https://sales.resevents.be/event/2362/yanns'
    ],
    (object) [
        'id' => 11,
        // 'name' => 'YANNS',
        'date_start' => '2026-10-24 20:00:00',
        'date_opening' => '2026-10-24 19:00:00',
        'city' => (object) ['name' => 'CASABLANCA'],
        'country' => (object) ['name' => 'Maroc'],
        'place' => (object) ['name' => 'Megarama'],
        'ticketUrl' => ''
    ],
    (object) [
        'id' => 12,
        // 'name' => 'YANNS',
        'date_start' => '2026-10-25 20:00:00',
        'date_opening' => '2026-10-25 19:00:00',
        'city' => (object) ['name' => 'RABAT'],
        'country' => (object) ['name' => 'Maroc'],
        'place' => (object) ['name' => 'Date à confirmer'],
        'ticketUrl' => ''
    ],
    (object) [
        'id' => 13,
        // 'name' => 'YANNS',
        'date_start' => '2026-10-25 20:00:00',
        'date_opening' => '2026-10-25 19:00:00',
        'city' => (object) ['name' => 'MARRAKECH'],
        'country' => (object) ['name' => 'Maroc'],
        'place' => (object) ['name' => 'Palais des Congrès (Date à confirmer)'],
        'ticketUrl' => ''
    ],
    (object) [
        'id' => 14,
        // 'name' => 'YANNS',
        'date_start' => '2026-12-06 17:00:00',
        'date_opening' => '2026-12-06 16:00:00',
        'city' => (object) ['name' => 'CLERMONT-FERRAND'],
        'country' => (object) ['name' => 'France'],
        'place' => (object) ['name' => 'La Coopérative de Mai'],
        'ticketUrl' => 'https://billetterie-lesdernierscouches.tickandlive.com/reserver/yanns/158022'
    ],
    (object) [
        'id' => 15,
        // 'name' => 'YANNS',
        'date_start' => '2027-02-06 00:00:00',
        'date_opening' => '',
        'city' => (object) ['name' => 'BRUXELLES'],
        'country' => (object) ['name' => 'Belgique'],
        'place' => (object) ['name' => 'La Madeleine'],
        'ticketUrl' => 'https://la-madeleine.be/show/yanns/',
        'timeTbd' => true
    ],
    (object) [
        'id' => 18,
        // 'name' => 'YANNS',
        'date_start' => '2027-03-13 00:00:00',
        'date_opening' => '',
        'city' => (object) ['name' => 'MONTRÉAL'],
        'country' => (object) ['name' => 'Canada'],
        'place' => (object) ['name' => 'L\'Olympia'],
        'ticketUrl' => 'https://www.ticketmaster.ca/event/310064FB995E4DF3?lang=fr-ca&brand=lolympia',
        'timeTbd' => true
    ],
    (object) [
        'id' => 16,
        // 'name' => 'YANNS',
        'date_start' => '2027-04-04 00:00:00',
        'date_opening' => '',
        'city' => (object) ['name' => 'MARSEILLE'],
        'country' => (object) ['name' => 'France'],
        'place' => (object) ['name' => 'Le Silo CEPAC'],
        'ticketUrl' => 'https://www.cepacsilo-marseille.fr/evenement/yanns/',
        'timeTbd' => true
    ],
    (object) [
        'id' => 23,
        'date_start' => '2027-06-12 20:00:00',
        'date_opening' => '2027-06-12 19:00:00',
        'city' => (object) ['name' => 'AMNEVILLE'],
        'country' => (object) ['name' => 'France'],
        'place' => (object) ['name' => 'Le Galaxie'],
        'ticketUrl' => 'https://www.fnacspectacles.com/event/yanns-phenomenal-re-tour-le-galaxie-amneville-22231469/?affiliate=QLC'
    ],
    (object) [
        'id' => 24,
        'date_start' => '2027-06-19 20:00:00',
        'date_opening' => '2027-06-19 19:00:00',
        'city' => (object) ['name' => 'PARIS'],
        'country' => (object) ['name' => 'France'],
        'place' => (object) ['name' => 'Le Dôme'],
        'ticketUrl' => 'https://www.fnacspectacles.com/event/yanns-phenomenal-re-tour-dome-de-paris-22234580/?affiliate=QLC'
    ],
    (object) [
        'id' => 25,
        'date_start' => '2027-11-07 20:00:00',
        'date_opening' => '2027-11-07 19:00:00',
        'city' => (object) ['name' => 'ROUEN'],
        'country' => (object) ['name' => 'France'],
        'place' => (object) ['name' => 'Zénith'],
        'ticketUrl' => 'https://www.fnacspectacles.com/event/yanns-phenomenal-re-tour-zenith-de-rouen-22202850/'
    ],
    (object) [
        'id' => 26,
        'date_start' => '2027-11-13 20:00:00',
        'date_opening' => '2027-11-13 19:00:00',
        'city' => (object) ['name' => 'LILLE'],
        'country' => (object) ['name' => 'France'],
        'place' => (object) ['name' => 'Zénith Arena'],
        'ticketUrl' => 'https://www.fnacspectacles.com/event/yanns-phenomenal-re-tour-zenith-arena-de-lille-22234579/?affiliate=QLC'
    ],
    (object) [
        'id' => 27,
        'date_start' => '2027-11-21 20:00:00',
        'date_opening' => '2027-11-21 19:00:00',
        'city' => (object) ['name' => 'DIJON'],
        'country' => (object) ['name' => 'France'],
        'place' => (object) ['name' => 'Zénith'],
        'ticketUrl' => 'https://www.fnacspectacles.com/event/yanns-phenomenal-re-tour-zenith-de-dijon-22234473/?affiliate=QLC'
    ]
];

$eventsAll = $eventsArray;
$eventsWithTicket = array_filter($eventsArray, function($ev) {
    return !empty($ev->ticketUrl) || !empty($ev->isFree);
});
$eventsLimit = array_slice($eventsWithTicket, 0, 6);

$checkoutUrl = 'https://checkout.lebillet.eu/';
$lebilletEventId = 1816;

// API endpoint: /api/products  → JSON com produtos do lebillet
if ($path === '/api/products') {
    header('Content-Type: application/json; charset=utf-8');
    $service = new \App\Services\LeBilletService();
    // Esconde da grid os produtos marcados como 'hidden' (ex.: Le Maillot
    // Criança, que só é acedido pelo seletor Enfant/Adulte na página).
    $gridProducts = array_values(array_filter(
        $service->getProducts($lebilletEventId),
        fn($p) => empty($p['hidden'])
    ));
    // Localiza o nome conforme o idioma (Criança/Adulto → Enfant/Adulte em FR).
    foreach ($gridProducts as &$gp) {
        $gp['name'] = \App\Helpers\I18n::localizeProductName($gp['name']);
    }
    unset($gp);
    echo json_encode(
        $gridProducts,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );
    exit;
}

if (array_key_exists($path, $routes)) {
    $activePage = $routes[$path];

    // Page titles
    $pageTitleMap = [
        'home' => 'page.home',
        'musique' => 'page.music',
        'dates' => 'page.dates',
        'collections' => 'page.collections',
        'biographie' => 'page.bio',
        'product_details' => 'page.product',
    ];
    $pageTitle = isset($pageTitleMap[$activePage])
        ? \App\Helpers\I18n::t($pageTitleMap[$activePage])
        : 'YANNS';
    ?>
    <!DOCTYPE html>
    <html lang="<?= htmlspecialchars(\App\Helpers\I18n::current()) ?>">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>YANNS -
            <?= htmlspecialchars($pageTitle) ?>
        </title>
        <link rel="icon" type="image/svg+xml" href="/assets/img/favicon.svg">

        <!-- Fonts: preconnect + non-render-blocking load (Inter) -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">

        <?php
        // CSS carregado como <link> versionados (cache-busting por filemtime),
        // na mesma ordem da cascata. Evita @import (que não recebe versão e
        // causava CSS em cache desatualizado após deploy).
        $cssFiles = [
            '/assets/css/variables.css',
            '/assets/css/components/button.css',
            '/assets/css/components/input.css',
            '/assets/css/components/menu-item.css',
            '/assets/css/components/menu.css',
            '/assets/css/components/mobile-menu.css',
            '/assets/css/components/store-card.css',
            '/assets/css/components/scroll-icon.css',
            '/assets/css/components/video-control.css',
            '/assets/css/components/sound-control.css',
            '/assets/css/components/music-service-button.css',
            '/assets/css/components/tab.css',
            '/assets/css/components/dropdown.css',
            '/assets/css/components/card-date.css',
            '/assets/css/components/card-calendar.css',
            '/assets/css/components/card-youtube.css',
            '/assets/css/components/modal-youtube.css',
            '/assets/css/components/announcement-bar.css',
            '/assets/css/components/hero.css',
            '/assets/css/components/dates-section.css',
            '/assets/css/components/dates-page.css',
            '/assets/css/components/videos-section.css',
            '/assets/css/components/boutique-section.css',
            '/assets/css/components/biography.css',
            '/assets/css/components/music-video.css',
            '/assets/css/components/collections.css',
            // '/assets/css/components/maillot-banner.css', // Le Maillot descontinuado
            '/assets/css/components/product-details.css',
            '/assets/css/layout.css',
            '/assets/css/styles.css',
            '/assets/css/components/cart-modal.css',
        ];
        foreach ($cssFiles as $css):
        ?>
        <link rel="stylesheet" href="<?= htmlspecialchars($asset($css)) ?>">
        <?php endforeach; ?>

        <!-- Toastify CSS — carregado sem bloquear o render -->
        <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css"
              media="print" onload="this.media='all'">
        <noscript><link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css"></noscript>
    </head>

    <body>
        <?php
        $headerActivePage = ($activePage === 'product_details') ? 'collections' : $activePage;
        ?>
        <?= Component::render('Header', ['activePage' => $headerActivePage]) ?>

        <main class="main-content">
            <?php if ($activePage === 'home'): ?>
                <?php // Component::render('AnnouncementBar') — Le Maillot descontinuado ?>
                <?= Component::render('Hero') ?>
                <?= Component::render('DatesSection', ['events' => $eventsLimit, 'checkoutUrl' => $checkoutUrl]) ?>
                <?= Component::render('VideosSection') ?>
                <?= Component::render('BoutiqueSection') ?>
                <?php
            elseif ($activePage === 'collections'): ?>
                <?php // Component::render('MaillotBanner') — Le Maillot descontinuado ?>
                <?= Component::render('Collections') ?>
                <?php
            elseif ($activePage === 'product_details'):
                $productService = new \App\Services\LeBilletService();
                $requestedId = isset($_GET['id']) ? (string) $_GET['id'] : '';
                $product = $productService->getProduct($lebilletEventId, $requestedId);
                // Nome localizado (ex.: "Le Maillot Adulto" → "Le Maillot Adulte" em FR)
                if ($product) {
                    $product['name'] = \App\Helpers\I18n::localizeProductName($product['name']);
                }
                // Produto irmão de cor (Blanc/Noir), quando existir.
                // As casquettes Blanche e Noir são produtos independentes (sem
                // seletor de cor entre elas). O agrupamento por cor fica restrito
                // aos t-shirts que tenham um color_sibling explícito no config.
                $sibling = null;
                $siblingKind = null;
                if ($product && $product['type'] === 'shirt') {
                    if ($colorSib = $productService->getColorSibling($lebilletEventId, $requestedId)) {
                        $sibling = $colorSib;
                        $siblingKind = 'color';
                    }
                }
                // Outra versão do mesmo produto (Le Maillot Adulto ↔ Criança):
                // exibida como box "Également disponible" na página.
                $related = $productService->getAgeSibling($lebilletEventId, $requestedId);
                if ($related) {
                    $related['name'] = \App\Helpers\I18n::localizeProductName($related['name']);
                }
                ?>
                <?= Component::render('ProductDetails', [
                    'product'      => $product,
                    'sibling'      => $sibling,
                    'siblingKind'  => $siblingKind,
                    'related'      => $related,
                    'eventId'      => $lebilletEventId,
                    'checkoutUrl'  => $checkoutUrl,
                ]) ?>
                <?php
            elseif ($activePage === 'dates'): ?>
                <?= Component::render('DatesPage', ['events' => $eventsAll, 'checkoutUrl' => $checkoutUrl]) ?>
                <?php
            elseif ($activePage === 'biographie'): ?>
                <?= Component::render('Biography') ?>
                <?php
            elseif ($activePage === 'musique'): ?>
                <?= Component::render('MusicVideo') ?>
                <?php
            else: ?>
                <section class="generic-page">
                    <div class="container">
                        <h1 class="generic-page-title">
                            <?= htmlspecialchars($pageTitle) ?>
                        </h1>
                    </div>
                </section>
                <?php
            endif; ?>
            <?= Component::render('checkoutModal', ['checkoutUrl' => $checkoutUrl]) ?>
        </main>

        <?= Component::render('Footer') ?>

        <!-- Toastify JS -->
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

        <script>window.LEBILLET_EVENT_ID = '<?= htmlspecialchars((string) $lebilletEventId) ?>';</script>
        <script src="<?= htmlspecialchars($asset('/assets/js/youtube-modal.js')) ?>"></script>
        <script src="<?= htmlspecialchars($asset('/assets/js/cart-modal.js')) ?>"></script>
        <script src="<?= htmlspecialchars($asset('/assets/js/hero.js')) ?>"></script>
        <?php if ($activePage === 'collections'): ?>
            <script src="<?= htmlspecialchars($asset('/assets/js/collections.js')) ?>"></script>
            <?php
        elseif ($activePage === 'product_details'): ?>
            <!--<script src="/assets/js/product-details.js"></script> -->
            <?php
        endif; ?>
    </body>

    </html>
    <?php
} else {
    http_response_code(404);
    echo "404 Not Found";
}
