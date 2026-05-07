<?php
/**
 * Proxy do checkout LeBillet — yanns
 *
 * GET  /checkout-proxy.php?event_id=1816&product_id=10944_133  → carrega checkout filtrado
 * POST /checkout-proxy.php?event_id=1816&relay=1&path=1816/cart/resume → relay AJAX
 */

$eventId   = preg_replace('/\D/', '', $_GET['event_id'] ?? '1816');
$productId = preg_replace('/[^0-9_]/', '', $_GET['product_id'] ?? '');
$qty       = max(0, (int) ($_GET['qty'] ?? 0));
// Filtramos pelo product_id COMPLETO (com variante). Se não tiver variante, usa só o produto.
$filterFullId = $productId; // ex: "10944_134" ou "10944"

// ─── MODO RELAY ────────────────────────────────────────────────────────────────
if (isset($_GET['relay'])) {
    $path = trim($_GET['path'] ?? '', '/');

    if (!preg_match('/^[\w\-\/\.]+$/', $path)) {
        http_response_code(400);
        exit('{"error":"invalid path"}');
    }

    $apiUrl = 'https://checkout.lebillet.eu/' . $path;
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    if (!in_array($method, ['GET', 'POST'], true)) {
        http_response_code(405);
        exit('{"error":"method not allowed"}');
    }
    $rawBody = file_get_contents('php://input');
    if (empty($rawBody) && $method === 'POST') {
        $rawBody = http_build_query($_POST);
    }

    $rawCt = $_SERVER['CONTENT_TYPE'] ?? '';
    $allowedCts = ['application/json', 'application/x-www-form-urlencoded', 'multipart/form-data', 'text/plain'];
    $ct = 'application/x-www-form-urlencoded';
    foreach ($allowedCts as $allowed) {
        if (stripos($rawCt, $allowed) === 0) {
            $ct = $allowed;
            break;
        }
    }

    $sidFromBody = '';
    if ($rawBody) {
        parse_str($rawBody, $parsedBody);
        $sidFromBody = preg_replace('/[^a-zA-Z0-9]/', '', $parsedBody['sid'] ?? '');
    }

    $reqHeaders = [
        'Content-Type: ' . $ct,
        'User-Agent: Mozilla/5.0',
        'Accept: */*',
        'X-Requested-With: XMLHttpRequest',
        'Referer: https://checkout.lebillet.eu/' . $eventId,
        'Origin: https://checkout.lebillet.eu',
    ];

    $cookieParts = [];
    if ($sidFromBody) {
        $cookieParts[] = 'PHPSESSID=' . rawurlencode($sidFromBody);
    }
    $ownCookies = ['PHPSESSID'];
    foreach ($_COOKIE as $k => $v) {
        if (!in_array($k, $ownCookies, true)) {
            $cookieParts[] = rawurlencode($k) . '=' . rawurlencode($v);
        }
    }
    if (!empty($cookieParts)) {
        $reqHeaders[] = 'Cookie: ' . implode('; ', $cookieParts);
    }

    $opts = [
        'http' => [
            'method' => $method,
            'header' => implode("\r\n", $reqHeaders),
            'content' => $rawBody,
            'timeout' => 10,
            'follow_location' => 1,
            'max_redirects' => 3,
        ],
        'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
    ];

    $result = @file_get_contents($apiUrl, false, stream_context_create($opts));

    foreach ($http_response_header ?? [] as $h) {
        if (stripos($h, 'Set-Cookie:') === 0) {
            $h = preg_replace('/;\s*domain=[^;]+/i', '', $h);
            $h = preg_replace('/;\s*secure\b/i', '', $h);
            $h = preg_replace('/;\s*samesite=[^;]+/i', '; SameSite=Lax', $h);
            header($h, false);
        }
        if (stripos($h, 'Content-Type:') === 0) {
            header($h);
        }
    }

    $requestOrigin = $_SERVER['HTTP_ORIGIN'] ?? '';
    $siteDomain = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
    if (empty($requestOrigin) || $requestOrigin === $siteDomain) {
        header('Access-Control-Allow-Origin: ' . $siteDomain);
    }
    header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
    header('X-Content-Type-Options: nosniff');

    echo $result !== false ? $result : '{"error":"relay failed"}';
    exit;
}

// ─── MODO NORMAL ───────────────────────────────────────────────────────────────
if (empty($eventId)) {
    http_response_code(400);
    exit('Parametro event_id em falta.');
}

$checkoutUrl = 'https://checkout.lebillet.eu/' . $eventId;

// Reencaminhar cookies do browser → lebillet, para preservar a sessão (carrinho)
// entre navegações do site. O cookie PHPSESSID foi setado pela lebillet em fetches
// anteriores (via Set-Cookie repassado por este proxy).
$ownCookies = ['PHPSESSID']; // não enviar cookies próprios deste site
$cookieHeader = '';
$cookieParts = [];
foreach ($_COOKIE as $k => $v) {
    if (in_array($k, $ownCookies, true)) continue;
    $cookieParts[] = rawurlencode($k) . '=' . rawurlencode($v);
}
// PHPSESSID que veio do browser (setado pela lebillet) precisa voltar para a lebillet
if (isset($_COOKIE['PHPSESSID'])) {
    $sid = preg_replace('/[^a-zA-Z0-9]/', '', $_COOKIE['PHPSESSID']);
    if ($sid) {
        $cookieParts[] = 'PHPSESSID=' . rawurlencode($sid);
    }
}
if (!empty($cookieParts)) {
    $cookieHeader = "Cookie: " . implode('; ', $cookieParts) . "\r\n";
}

$opts = [
    'http' => [
        'method' => 'GET',
        'timeout' => 15,
        'follow_location' => 1,
        'header' =>
            "User-Agent: Mozilla/5.0\r\n" .
            "Accept: text/html\r\n" .
            "Accept-Language: fr-FR,fr;q=0.9\r\n" .
            $cookieHeader,
    ],
    'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
];

$html = @file_get_contents($checkoutUrl, false, stream_context_create($opts));

if ($html === false) {
    http_response_code(502);
    exit('Nao foi possivel carregar o checkout.');
}

foreach ($http_response_header ?? [] as $h) {
    if (stripos($h, 'Set-Cookie:') === 0) {
        $h = preg_replace('/;\s*domain=[^;]+/i', '', $h);
        $h = preg_replace('/;\s*secure\b/i', '', $h);
        $h = preg_replace('/;\s*samesite=[^;]+/i', '; SameSite=Lax', $h);
        header($h, false);
    }
}

// Remover crossorigin/integrity (evita bloqueios CORS de assets da lebillet)
$html = preg_replace('/\s+crossorigin=(["\'])[^"\']*\1/i', '', $html);
$html = preg_replace('/\s+integrity=(["\'])[^"\']*\1/i', '', $html);

// Reescrever URLs relativas → absolutas
$base = 'https://checkout.lebillet.eu';

$html = preg_replace_callback(
    '/\b(src|href)\s*=\s*(["\'])(?!https?:\/\/|\/\/|#|javascript:|data:|mailto:)([^"\']+)\2/i',
    function ($m) use ($base) {
        return $m[1] . '=' . $m[2] . $base . '/' . ltrim($m[3], '/') . $m[2];
    },
    $html
);

$html = preg_replace_callback(
    '/\baction\s*=\s*(["\'])(?!https?:\/\/|\/\/)([^"\']+)\1/i',
    function ($m) use ($base) {
        return 'action=' . $m[1] . $base . '/' . ltrim($m[2], '/') . $m[1];
    },
    $html
);

// Redirecionar BASE_URL para o nosso proxy relay
$relayBase = '/checkout-proxy.php?event_id=' . $eventId . '&relay=1&path=';
$html = str_replace(
    'var BASE_URL = "https://checkout.lebillet.eu/";',
    'var BASE_URL = "' . $relayBase . '";',
    $html
);

// Mobile fix + esconder bolhas decorativas
$mobileFix = '
<style id="proxy-mobile-fix">
.blob, .blob1, .blob2 { display: none !important; }

@media (max-width: 768px) {
    html, body, .wrapper, .main-panel, .content, div.cart-sidebar {
        height: auto !important;
        max-height: none !important;
        overflow-y: auto !important;
        -webkit-overflow-scrolling: touch !important;
    }
    div.cart-sidebar {
        position: relative !important;
        bottom: auto !important;
        left: auto !important;
        width: 100% !important;
        margin-top: 20px !important;
        transform: none !important;
        display: block !important;
        height: auto !important;
        min-height: 0 !important;
        padding-bottom: 140px !important;
    }
    .cart-sidebar-content,
    .resume-cart,
    .resume-cart-content {
        display: block !important;
        height: auto !important;
        visibility: visible !important;
        opacity: 1 !important;
    }
    #button-cart-sider { display: none !important; }
    form, .content, .main-panel, #tickets-container, .wrapper, .calema-section, .map-card, .off-date, .map-card .off-date, .div-table-product, .container-fluid, .row.cart-content {
        margin-bottom: 0 !important;
        padding-bottom: 0 !important;
        min-height: 0 !important;
        max-height: none !important;
        height: auto !important;
        align-items: flex-start !important;
    }
    body { padding-bottom: 160px !important; }
}
</style>
';

// Esconder produtos iniciais se houver filterFullId (evita FOUC)
$filterCss = '';
if ($filterFullId !== '') {
    $filterCss = '
<style id="proxy-filter-fix">
table.ticket-box, .ticket-box { display: none !important; }
</style>';
}

// Filter JS — esconde todos ticket-box exceto o do produto+variante selecionado
// e pré-seleciona a quantidade no select correspondente
$filterScript = '';
if ($filterFullId !== '') {
    $filterScript = '
<script id="proxy-filter-js">
(function(){
    var fullId = "' . $filterFullId . '";
    var initialQty = ' . $qty . ';
    window._yannsFilterFullId = fullId;
    window._yannsInitialQty = initialQty;
    window._yannsQtyApplied = false;

    var _originalAddProducts;
    function hookAddProducts() {
        if (typeof window.addProducts === "function" && !window._proxyFilterHooked) {
            _originalAddProducts = window.addProducts;
            window.addProducts = function() {
                _originalAddProducts.apply(this, arguments);
                setTimeout(function(){ applyProductFilter(window._yannsFilterFullId); }, 60);
            };
            window._proxyFilterHooked = true;
            applyProductFilter(fullId);
            applyInitialQty();
        }
    }

    function applyInitialQty() {
        if (window._yannsQtyApplied) return;
        var qty = window._yannsInitialQty || 0;
        if (qty < 1) { window._yannsQtyApplied = true; return; }
        var fid = window._yannsFilterFullId || "";
        if (!fid || fid.indexOf("_") === -1) return;
        var sel = document.getElementById("product_" + fid);
        if (!sel) return;
        // Limita à opção máxima disponível
        var max = 0;
        sel.querySelectorAll("option").forEach(function(o){
            var v = parseInt(o.value, 10);
            if (!isNaN(v) && v > max) max = v;
        });
        var target = Math.min(qty, max || qty);
        if (parseInt(sel.value, 10) !== target) {
            sel.value = String(target);
            if (typeof window.addProducts === "function") {
                window.addProducts();
            } else {
                sel.dispatchEvent(new Event("change", { bubbles: true }));
            }
        }
        window._yannsQtyApplied = true;
    }

    var filterTimeout;
    new MutationObserver(function() {
        if(filterTimeout) clearTimeout(filterTimeout);
        filterTimeout = setTimeout(function() {
            hookAddProducts();
            applyProductFilter(window._yannsFilterFullId);
            applyInitialQty();
        }, 50);
    }).observe(document.documentElement, { childList: true, subtree: true });

    [0, 200, 600, 1500, 3000].forEach(function(ms) {
        setTimeout(function() {
            hookAddProducts();
            applyProductFilter(window._yannsFilterFullId);
            applyInitialQty();
        }, ms);
    });
})();
</script>';
}

// Ouve postMessage para atualizar filtro/quantidade sem reload
$updateFilterScript = '
<script id="proxy-postmessage-filter">
window.addEventListener("message", function(e) {
    if (!e.data || e.data.type !== "yannsUpdateFilter") return;
    var fullId = String(e.data.productId || "");
    window._yannsFilterFullId = fullId;
    applyProductFilter(fullId);
    if (typeof e.data.qty === "number" && e.data.qty > 0 && fullId.indexOf("_") !== -1) {
        var sel = document.getElementById("product_" + fullId);
        if (sel) {
            var max = 0;
            sel.querySelectorAll("option").forEach(function(o){
                var v = parseInt(o.value, 10);
                if (!isNaN(v) && v > max) max = v;
            });
            var target = Math.min(e.data.qty, max || e.data.qty);
            if (parseInt(sel.value, 10) !== target) {
                sel.value = String(target);
                if (typeof window.addProducts === "function") window.addProducts();
                else sel.dispatchEvent(new Event("change", { bubbles: true }));
            }
        }
    }
});

function applyProductFilter(fullId) {
    var tables = document.querySelectorAll(".div-table-product table.ticket-box");
    if (!tables.length) return;
    if (!fullId) {
        tables.forEach(function(t){ t.style.setProperty("display", "table", "important"); });
        return;
    }
    var hasVariant = fullId.indexOf("_") !== -1;
    tables.forEach(function(t) {
        var match;
        if (hasVariant) {
            // Filtro exato por variante: id="product_10944_134"
            match = t.querySelector("[id=\"product_" + fullId + "\"]");
        } else {
            // Sem variante: pega qualquer variante do produto
            match = t.querySelector("[id^=\"product_" + fullId + "_\"]");
        }
        t.style.setProperty("display", match ? "table" : "none", "important");
    });
}
</script>';

// Cart sync — postMessage com contagem ao parent
$cartSyncScript = '
<script id="proxy-cart-sync">
(function(){
    function totalQty() {
        var total = 0;
        document.querySelectorAll("select.quantity-select").forEach(function(s) {
            total += parseInt(s.value || "0", 10);
        });
        return total;
    }
    function notify() {
        window.parent.postMessage({ type: "yannsCartCount", count: totalQty() }, "*");
    }
    document.addEventListener("click", function(e) {
        if (e.target && e.target.classList.contains("button-select-product")) {
            setTimeout(notify, 150);
        }
    });
    document.addEventListener("change", function(e) {
        if (e.target && e.target.classList.contains("quantity-select")) {
            notify();
        }
    });
})();
</script>';

$html = str_replace('</head>', $mobileFix . $filterCss . '</head>', $html);
$html = str_replace('</body>', $updateFilterScript . $filterScript . $cartSyncScript . '</body>', $html);

header('Content-Type: text/html; charset=utf-8');
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: payment=(self)');
echo $html;
