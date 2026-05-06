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
$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://lebillet.eu/api_events/events?limit=6',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_HTTPHEADER => array(
        'Authorization: Basic 9f4c2a1b7e3d6a8cpewe8992801',
        'API: application/json',
        'Content-Type: application/json',
        'Cookie: PHPSESSID=7sooqfe3a52859s597k89fb9m5'
    ),
));

$response = json_decode(curl_exec($curl));
$eventsLimit = $response->events;

$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://lebillet.eu/api_events/events',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_HTTPHEADER => array(
        'Authorization: Basic 9f4c2a1b7e3d6a8cpewe8992801',
        'API: application/json',
        'Content-Type: application/json',
        'Cookie: PHPSESSID=7sooqfe3a52859s597k89fb9m5'
    ),
));

$response = json_decode(curl_exec($curl));
$eventsAll = $response->events;

$checkoutUrl = 'https://checkout.lebillet.eu/';

if (array_key_exists($path, $routes)) {
    $activePage = $routes[$path];

    // Page titles
    $pageTitles = [
        'home' => 'Home',
        'musique' => 'Musique & Vidéo',
        'dates' => 'Dates',
        'collections' => 'COLLECTIONS',
        'biographie' => 'Biographie',
        'product_details' => 'Détails du Produit'
    ];
    $pageTitle = $pageTitles[$activePage] ?? 'YANNS';
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>YANNS -
            <?= htmlspecialchars($pageTitle) ?>
        </title>
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
            elseif ($activePage === 'product_details'): ?>
                <?= Component::render('ProductDetails') ?>
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
        <script src="/assets/js/cart-modal.js"></script>
        <script src="/assets/js/hero.js"></script>
        <?php if ($activePage === 'collections'): ?>
            <script src="/assets/js/collections.js"></script>
        <?php
        elseif ($activePage === 'product_details'): ?>
            <script src="/assets/js/product-details.js"></script>
        <?php
        endif; ?>
    </body>

    </html>
<?php
} else {
    http_response_code(404);
    echo "404 Not Found";
}
