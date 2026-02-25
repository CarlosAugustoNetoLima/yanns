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
    '/biographie' => 'biographie'
];

if (array_key_exists($path, $routes)) {
    $activePage = $routes[$path];

    // Page titles
    $pageTitles = [
        'home' => 'Home',
        'musique' => 'Musique & Vidéo',
        'dates' => 'Dates',
        'collections' => 'Collections',
        'biographie' => 'Biographie'
    ];
    $pageTitle = $pageTitles[$activePage] ?? 'YANNS';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YANNS -
        <?= htmlspecialchars($pageTitle)?>
    </title>
    <link rel="stylesheet" href="/assets/css/styles.css">
</head>

<body>
    <?= Component::render('Header', ['activePage' => $activePage])?>

    <main class="main-content">
        <?php if ($activePage === 'home'): ?>
        <?= Component::render('Hero')?>
        <?= Component::render('DatesSection')?>
        <?= Component::render('VideosSection')?>
        <?= Component::render('BoutiqueSection')?>
        <?php
    else: ?>
        <section class="generic-page">
            <div class="container">
                <h1 class="generic-page-title">
                    <?= htmlspecialchars($pageTitle)?>
                </h1>
            </div>
        </section>
        <?php
    endif; ?>
    </main>

    <?= Component::render('Footer')?>

    <script src="/assets/js/youtube-modal.js"></script>
    <script src="/assets/js/hero.js"></script>
</body>

</html>
<?php
}
else {
    http_response_code(404);
    echo "404 Not Found";
}