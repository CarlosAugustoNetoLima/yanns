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
        'id' => 1,
        // 'name' => 'YANNS "Phénoménal RE/TOUR"',
        'date_start' => '2026-07-20 21:00:00',
        'date_opening' => '2026-07-20 20:00:00',
        'city' => (object) ['name' => 'CAP D\'AGDE'],
        'country' => (object) ['name' => 'France'],
        'place' => (object) ['name' => 'Les Arènes du Cap d\'Agde'],
        'ticketUrl' => 'https://my.weezevent.com/yanns-phenomenal-retour-1'
    ],
    (object) [
        'id' => 2,
        // 'name' => 'YANNS',
        'date_start' => '2026-08-09 21:30:00',
        'date_opening' => '2026-08-09 20:30:00',
        'city' => (object) ['name' => 'PALAVAS-LES-FLOTS'],
        'country' => (object) ['name' => 'France'],
        'place' => (object) ['name' => 'Festival Côté Mer'],
        'ticketUrl' => 'https://shotgun.live/events/festival-cote-mer-yanns'
    ],
    (object) [
        'id' => 3,
        // 'name' => 'YANNS',
        'date_start' => '2026-09-11 20:30:00',
        'date_opening' => '2026-09-11 19:30:00',
        'city' => (object) ['name' => 'BOURGES'],
        'country' => (object) ['name' => 'France'],
        'place' => (object) ['name' => 'Palais d\'Auron'],
        'ticketUrl' => 'https://my.weezevent.com/yanns-phenomenal-retour'
    ],
    (object) [
        'id' => 4,
        // 'name' => 'YANNS',
        'date_start' => '2026-09-12 20:00:00',
        'date_opening' => '2026-09-12 19:00:00',
        'city' => (object) ['name' => 'CALUIRE-ET-CUIRE'],
        'country' => (object) ['name' => 'France'],
        'place' => (object) ['name' => 'Radiant-Bellevue'],
        'ticketUrl' => 'https://billetterie.radiant-bellevue.fr/fr/product/1205/radiant_bellevue/yanns'
    ],
    (object) [
        'id' => 5,
        // 'name' => 'YANNS',
        'date_start' => '2026-09-13 17:00:00',
        'date_opening' => '2026-09-13 16:00:00',
        'city' => (object) ['name' => 'REIMS'],
        'country' => (object) ['name' => 'France'],
        'place' => (object) ['name' => 'LE K'],
        'ticketUrl' => 'https://my.weezevent.com/yanns-phenomenal-retour-2'
    ],
    (object) [
        'id' => 6,
        // 'name' => 'YANNS',
        'date_start' => '2026-10-03 20:30:00',
        'date_opening' => '2026-10-03 19:30:00',
        'city' => (object) ['name' => 'DISON'],
        'country' => (object) ['name' => 'Belgique'],
        'place' => (object) ['name' => 'Espace 58'],
        'ticketUrl' => 'https://sales.resevents.be/event/2362/yanns'
    ],
    (object) [
        'id' => 7,
        // 'name' => 'YANNS',
        'date_start' => '2026-12-06 17:00:00',
        'date_opening' => '2026-12-06 16:00:00',
        'city' => (object) ['name' => 'CLERMONT-FERRAND'],
        'country' => (object) ['name' => 'France'],
        'place' => (object) ['name' => 'La Coopérative de Mai'],
        'ticketUrl' => 'https://billetterie-lesdernierscouches.tickandlive.com/reserver/yanns/158022'
    ]
];

$eventsAll = $eventsArray;
$eventsLimit = array_slice($eventsArray, 0, 6);

$checkoutUrl = 'https://checkout.lebillet.eu/';
$lebilletEventId = 1816;

// API endpoint: /api/products  → JSON com produtos do lebillet
if ($path === '/api/products') {
    header('Content-Type: application/json; charset=utf-8');
    $service = new \App\Services\LeBilletService();
    echo json_encode(
        $service->getProducts($lebilletEventId),
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
        <link rel="stylesheet" href="/assets/css/styles.css">
        <link rel="stylesheet" href="/assets/css/components/cart-modal.css">
        <!-- Toastify CSS -->
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    </head>

    <body>
        <?php
        $headerActivePage = ($activePage === 'product_details') ? 'collections' : $activePage;
        ?>
        <?= Component::render('Header', ['activePage' => $headerActivePage]) ?>

        <main class="main-content">
            <?php if ($activePage === 'home'): ?>
                <?= Component::render('Hero') ?>
                <?= Component::render('DatesSection', ['events' => $eventsLimit, 'checkoutUrl' => $checkoutUrl]) ?>
                <?= Component::render('VideosSection') ?>
                <?= Component::render('BoutiqueSection') ?>
                <?php
            elseif ($activePage === 'collections'): ?>
                <?= Component::render('Collections') ?>
                <?php
            elseif ($activePage === 'product_details'):
                $productService = new \App\Services\LeBilletService();
                $requestedId = isset($_GET['id']) ? (string) $_GET['id'] : '';
                $product = $productService->getProduct($lebilletEventId, $requestedId);
                $sibling = ($product && $product['type'] === 'casquette')
                    ? $productService->getCasquetteSibling($lebilletEventId, $requestedId)
                    : null;
                ?>
                <?= Component::render('ProductDetails', [
                    'product'      => $product,
                    'sibling'      => $sibling,
                    'eventId'      => $lebilletEventId,
                    'checkoutUrl'  => $checkoutUrl,
                ]) ?>
                <?php
            elseif ($activePage === 'dates'): ?>
                <?= Component::render('DatesPage', ['events' => $eventsAll, 'checkoutUrl' => $checkoutUrl]) ?>
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
        <?= Component::render('CartModal') ?>

        <!-- Toastify JS -->
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

        <script src="/assets/js/youtube-modal.js"></script>
        <!--<script src="/assets/js/cart-modal.js"></script>-->
        <script src="/assets/js/hero.js"></script>
        <?php if ($activePage === 'collections'): ?>
            <script src="/assets/js/collections.js"></script>
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
