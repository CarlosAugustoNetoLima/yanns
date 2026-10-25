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
// Modo "carrinho": abre mostrando APENAS os itens já no carrinho (qty > 0),
// em vez do catálogo inteiro. Usado pelo ícone de carrinho do header.
$cartView = (($_GET['view'] ?? '') === 'cart');

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
    } elseif (isset($_COOKIE['PHPSESSID'])) {
        // Fallback: usa o PHPSESSID que o browser já tem (setado em fetches
        // anteriores via Set-Cookie repassado por este proxy). Necessário para
        // reconciliar o carrinho (ex: GET cart/resume na carga da página).
        $sidCookie = preg_replace('/[^a-zA-Z0-9]/', '', $_COOKIE['PHPSESSID']);
        if ($sidCookie) {
            $cookieParts[] = 'PHPSESSID=' . rawurlencode($sidCookie);
        }
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

// Idioma para o checkout da LeBillet (formato l=xx_xx), conforme o idioma do
// site (cookie yanns_lang). FR por defeito.
$langMap  = ['fr' => 'fr_fr', 'pt' => 'pt_pt', 'en' => 'en_us'];
$siteLang = $_COOKIE['yanns_lang'] ?? 'fr';
$lbLang   = $langMap[$siteLang] ?? 'fr_fr';

$checkoutUrl = 'https://checkout.lebillet.eu/' . $eventId . '?l=' . $lbLang;

// ─── Mapa de cor por produto (config 'color') ───────────────────────────────
// Algumas casquettes partilham no LeBillet a MESMA variante (rotulada "Blanc"),
// por isso a casquette Noir aparece como "Blanc" na lista e no resumo do carrinho.
// Como a cor é um atributo do produto, montamos id_produto → rótulo de cor
// localizado para corrigir o rótulo da variante (ver $colorFixScript abaixo).
$idColorMap = [];
$productConfig = @require __DIR__ . '/../app/Config/products.php';
$langDict      = @require __DIR__ . "/../app/Config/lang/{$siteLang}.php";
if (!is_array($langDict)) {
    $langDict = @require __DIR__ . '/../app/Config/lang/fr.php';
}
if (is_array($productConfig) && is_array($langDict)) {
    foreach ($productConfig as $pid => $cfg) {
        if (!empty($cfg['color'])) {
            $idColorMap[(string) $pid] = $langDict['product.color.' . $cfg['color']] ?? $cfg['color'];
        }
    }
}
$idColorJson = json_encode($idColorMap, JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_HEX_TAG);

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

/* Impede que o símbolo de moeda quebre linha separado do valor no resumo do
   carrinho (a coluna é mais estreita no nosso modal do que no site da LeBillet) */
.small_value_product,
.table-cart-resume td,
.resume-cart td,
.cart-resume td { white-space: nowrap !important; }

@media (max-width: 768px) {
    html, body, .wrapper, .main-panel, .content {
        background: #ffffff !important;
        background-image: none !important;
    }
    html, body {
        height: 100% !important;
        max-height: 100% !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
        -webkit-overflow-scrolling: touch !important;
        overscroll-behavior: contain !important;
        touch-action: pan-y !important;
    }
    .wrapper, .main-panel, .content, div.cart-sidebar {
        height: auto !important;
        max-height: none !important;
        overflow: visible !important;
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
        padding-bottom: 16px !important;
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
    body { padding-bottom: 0 !important; }
}
</style>
';

// Esconder produtos iniciais se houver filterFullId (evita FOUC enquanto o
// filterScript decide qual ticket-box mostrar). NÃO cobrimos a página com
// overlay — o usuário precisa ver a tela "Merchandising Yanns" do LeBillet
// e clicar em Continuar manualmente.
$filterCss = '';
if ($filterFullId !== '' || $cartView) {
    $filterCss = '
<style id="proxy-filter-fix">
table.ticket-box, .ticket-box { display: none !important; }
</style>';
}

// Filter JS — no modo produto, esconde todos ticket-box exceto o do produto+variante
// e pré-seleciona a quantidade; no modo carrinho, mostra só os que têm qty > 0.
$filterScript = '';
if ($filterFullId !== '' || $cartView) {
    $filterScript = '
<script id="proxy-filter-js">
(function(){
    var fullId = "' . $filterFullId . '";
    var initialQty = ' . $qty . ';
    window._yannsFilterFullId = fullId;
    window._yannsCartView = ' . ($cartView ? 'true' : 'false') . ';
    window._yannsInitialQty = initialQty;
    window._yannsQtyApplied = false;

    // Aplica o filtro ativo conforme o modo (carrinho vs produto único).
    function applyActiveFilter() {
        if (window._yannsCartView) applyCartFilter();
        else applyProductFilter(window._yannsFilterFullId);
    }

    var _originalAddProducts;
    function hookAddProducts() {
        if (typeof window.addProducts === "function" && !window._proxyFilterHooked) {
            _originalAddProducts = window.addProducts;
            window.addProducts = function() {
                _originalAddProducts.apply(this, arguments);
                setTimeout(applyActiveFilter, 60);
            };
            window._proxyFilterHooked = true;
            applyActiveFilter();
            applyInitialQty();
        }
    }

    function applyInitialQty() {
        if (window._yannsQtyApplied) return;
        if (window._yannsCartView) { window._yannsQtyApplied = true; return; } // carrinho não pré-seleciona
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
            applyActiveFilter();
            applyInitialQty();
        }, 50);
    }).observe(document.documentElement, { childList: true, subtree: true });

    [0, 200, 600, 1500, 3000].forEach(function(ms) {
        setTimeout(function() {
            hookAddProducts();
            applyActiveFilter();
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
    window._yannsCartView = !!e.data.cartView;
    if (window._yannsCartView) { applyCartFilter(); return; } // modo carrinho: só itens com qty > 0
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

// Modo carrinho: mostra apenas os ticket-box cujo <select> tem quantidade > 0
// (os itens efetivamente no carrinho), escondendo o resto do catálogo.
function applyCartFilter() {
    var tables = document.querySelectorAll(".div-table-product table.ticket-box");
    if (!tables.length) return;
    tables.forEach(function(t) {
        var sel = t.querySelector("select[id^=\"product_\"]");
        var qty = sel ? parseInt(sel.value, 10) : 0;
        t.style.setProperty("display", qty > 0 ? "table" : "none", "important");
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
        var t = e.target;
        if (!t || !t.closest) return;
        // + / - de produto, ou botão de remover item do carrinho (remProducts)
        if (t.closest(".button-select-product") ||
            t.closest("[onclick*=\"remProducts\"]") ||
            (t.closest("button.close") && t.closest(".table-cart-resume"))) {
            setTimeout(notify, 300);
        }
    });
    document.addEventListener("change", function(e) {
        if (e.target && e.target.classList.contains("quantity-select")) {
            notify();
        }
    });
})();
</script>';

// Unfix mobile — neutraliza qualquer position:fixed/sticky para impedir botões sobrepostos
$unfixScript = '
<script id="proxy-unfix-mobile">
(function(){
    if (!window.matchMedia("(max-width: 768px)").matches) return;
    function unfix() {
        var els = document.body.querySelectorAll("*");
        for (var i = 0; i < els.length; i++) {
            var el = els[i];
            var cs = getComputedStyle(el);
            if (cs.position === "fixed" || cs.position === "sticky") {
                el.style.setProperty("position", "static", "important");
                el.style.setProperty("bottom", "auto", "important");
                el.style.setProperty("top", "auto", "important");
                el.style.setProperty("left", "auto", "important");
                el.style.setProperty("right", "auto", "important");
                el.style.setProperty("transform", "none", "important");
            }
        }
    }
    var pending = null;
    function schedule() {
        if (pending) cancelAnimationFrame(pending);
        pending = requestAnimationFrame(function(){ pending = null; unfix(); });
    }
    window.addEventListener("load", schedule);
    if (window.MutationObserver) {
        new MutationObserver(schedule).observe(document.documentElement,
            { childList: true, subtree: true, attributes: true, attributeFilter: ["style", "class"] });
    }
    setInterval(schedule, 800);
    schedule();
})();
</script>';

// Height reporter — para o iframe se ajustar ao conteúdo no mobile
$heightReporterScript = '
<script id="proxy-height-reporter">
(function(){
    var lastHeight = 0;
    function reportHeight() {
        var h = Math.max(
            document.body.scrollHeight,
            document.documentElement.scrollHeight
        );
        if (h !== lastHeight && h > 0) {
            lastHeight = h;
            window.parent.postMessage({ type: "yannsIframeHeight", height: h }, "*");
        }
    }
    var pending = null;
    function schedule() {
        if (pending) cancelAnimationFrame(pending);
        pending = requestAnimationFrame(function(){ pending = null; reportHeight(); });
    }
    window.addEventListener("load", schedule);
    window.addEventListener("resize", schedule);
    if (window.ResizeObserver) {
        var ro = new ResizeObserver(schedule);
        ro.observe(document.documentElement);
        if (document.body) ro.observe(document.body);
    }
    if (window.MutationObserver) {
        var mo = new MutationObserver(schedule);
        mo.observe(document.documentElement, { childList: true, subtree: true, attributes: true });
    }
    setInterval(schedule, 1000);
    schedule();
})();
</script>';

// Telefone com país padrão França (+33). Os <select> de país do telefone
// (selectMobileNumber / selectWhatsapp) vêm sem opção pré-selecionada e o
// LeBillet assume Portugal. Forçamos a França na 1ª vez, respeitando depois a
// escolha do utilizador.
$phoneFrScript = '
<script id="proxy-default-phone-fr">
(function(){
    // IMPORTANTE: o LeBillet carrega jQuery 2x e o select2 fica noutra instância,
    // por isso jQuery.trigger("change") NÃO sincroniza o widget. Usamos evento
    // NATIVO (dispatchEvent), que atravessa as instâncias e atualiza o select2.
    function rendered(el){
        var sib = el.nextElementSibling;
        var r = (sib && sib.querySelector) ? sib.querySelector(".select2-selection__rendered") : null;
        return { hasSelect2: !!(sib && /select2/.test(sib.className || "")), text: r ? (r.textContent || "") : "" };
    }
    function applyFr(el){
        if (!el || el.dataset.yannsFrOk) return;
        if (!el.options || el.options.length < 2) return; // ainda não populado
        var v = el.value;
        if (v && v !== " " && v !== "+351" && v !== "+33") { el.dataset.yannsFrOk = "1"; return; } // já escolhido
        var hasFr = Array.prototype.some.call(el.options, function(o){ return o.value === "+33"; });
        if (!hasFr) return;
        el.value = "+33";
        el.dispatchEvent(new Event("change", { bubbles: true }));
        var r = rendered(el);
        if (!r.hasSelect2 || /\+33|FRA/i.test(r.text)) el.dataset.yannsFrOk = "1";
    }
    function scan(){
        document.querySelectorAll(\'select[onchange*="selectCountry"]\').forEach(applyFr);
    }
    if (window.MutationObserver){
        new MutationObserver(scan).observe(document.documentElement, { childList: true, subtree: true });
    }
    [400, 900, 1800, 3000, 4500].forEach(function(ms){ setTimeout(scan, ms); });
})();
</script>';

// (a) País do endereço → França por defeito (dispara recarga de cidades).
// (b) Corrige rótulos que a tradução do LeBillet deixou por traduzir (aparecem
//     como chave crua, ex.: "send_message_ticket", "email_address_confirm").
//     Como o LeBillet traduz no cliente, corrigimos no DOM — NO IDIOMA do site.
$labelFixByLang = [
    'fr' => [
        'full_name_apelido'      => 'Nom de famille',
        'email_address_confirm'  => "Confirmer l'e-mail",
        'email_confirm'          => "Confirmer l'e-mail",
        'Mobile Number Confirm'  => 'Confirmer le numéro',
        'Mobile Number Confi'    => 'Confirmer le numéro',
        'send_message_ticket'    => 'Les billets seront envoyés par e-mail et WhatsApp.',
    ],
    'en' => [
        'full_name_apelido'      => 'Last name',
        'email_address_confirm'  => 'Confirm email',
        'email_confirm'          => 'Confirm email',
        'Mobile Number Confirm'  => 'Confirm number',
        'Mobile Number Confi'    => 'Confirm number',
        'send_message_ticket'    => 'Tickets will be sent by email and WhatsApp.',
    ],
    'pt' => [
        'full_name_apelido'      => 'Apelido',
        'email_address_confirm'  => 'Confirmar e-mail',
        'email_confirm'          => 'Confirmar e-mail',
        'Mobile Number Confirm'  => 'Confirmar número',
        'Mobile Number Confi'    => 'Confirmar número',
        'send_message_ticket'    => 'Os bilhetes serão enviados por e-mail e WhatsApp.',
    ],
];
$dictJson = json_encode($labelFixByLang[$siteLang] ?? $labelFixByLang['fr'], JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_HEX_TAG);

$frFixScript = '
<script id="proxy-fr-fix">
(function(){
    function setCountryFr(){
        var s = document.getElementById("register_country");
        if (!s || s.dataset.yannsCountryDone) return;
        if (!s.options || s.options.length < 2) return;
        var cur = s.value;
        if (cur && cur !== "" && cur !== "PRT") { s.dataset.yannsCountryDone = "1"; return; }
        var fr = Array.prototype.find.call(s.options, function(o){ return /fran[çc]a|france/i.test(o.text); });
        if (!fr) return;
        s.dataset.yannsCountryDone = "1";
        s.value = fr.value;
        s.dispatchEvent(new Event("change", { bubbles: true })); // evento nativo (dispara loadingCities)
    }
    var DICT = ' . $dictJson . ';
    function fixLabels(){
        document.querySelectorAll("label,h5,b,p,span,small,div").forEach(function(n){
            if (n.children.length) return;
            var t = n.textContent.trim();
            if (DICT[t]) n.textContent = DICT[t];
        });
        document.querySelectorAll("input[placeholder]").forEach(function(i){
            var p = i.getAttribute("placeholder");
            if (DICT[p]) i.setAttribute("placeholder", DICT[p]);
        });
    }
    function run(){ try { setCountryFr(); fixLabels(); } catch(e){} }
    if (window.MutationObserver){
        new MutationObserver(run).observe(document.documentElement, { childList: true, subtree: true });
    }
    [400, 1000, 2000, 3500].forEach(function(ms){ setTimeout(run, ms); });
})();
</script>';

// Corrige o rótulo da variante (cor) para refletir o produto escolhido, tanto na
// lista de produtos (ticket-box, ancorado pelo id do <select>) como no resumo do
// carrinho (casando pelo nome do produto). Só atua nos produtos com cor definida.
$colorFixScript = '
<script id="proxy-color-fix">
(function(){
    var ID_COLOR = ' . $idColorJson . ';
    if (!ID_COLOR || !Object.keys(ID_COLOR).length) return;
    var COLOR_RE = /(Blanc(?:he)?|Noire?|White|Black|Branc[oa]|Pret[oa])/i;
    var nameColor = {};           // nome do produto (normalizado) -> cor correta
    function norm(s){ return (s || "").replace(/\s+/g, " ").trim(); }

    function fixTicketBoxes(){
        document.querySelectorAll(\'select[id^="product_"]\').forEach(function(sel){
            var m = sel.id.match(/^product_(\d+)_/);
            if (!m) return;
            var color = ID_COLOR[m[1]];
            if (!color) return;
            var box = sel.closest("table.ticket-box") || sel.closest(".ticket-box");
            if (!box) return;
            var h5 = box.querySelector("h5");
            if (h5) nameColor[norm(h5.textContent)] = color;
            // Rótulo da variante: <span> simples com palavra de cor (não toca no
            // nome, que está no <h5>, nem no preço, que não é palavra de cor).
            box.querySelectorAll("span").forEach(function(sp){
                if (sp.children.length) return;
                var t = sp.textContent.trim();
                if (t && COLOR_RE.test(t) && t !== color) sp.textContent = color;
            });
        });
    }

    function fixResume(){
        document.querySelectorAll(".cart-resume td, .table-cart-resume td, .cart td").forEach(function(td){
            var small = td.querySelector("small");
            if (!small || !COLOR_RE.test(small.textContent)) return;
            var txt = norm(td.textContent);
            for (var name in nameColor){
                if (name && txt.indexOf(name) !== -1){
                    var fixed = small.innerHTML.replace(COLOR_RE, nameColor[name]);
                    if (fixed !== small.innerHTML) small.innerHTML = fixed;
                    break;
                }
            }
        });
    }

    function run(){ try { fixTicketBoxes(); fixResume(); } catch(e){} }
    if (window.MutationObserver){
        new MutationObserver(run).observe(document.documentElement, { childList: true, subtree: true });
    }
    [0, 200, 600, 1500, 3000].forEach(function(ms){ setTimeout(run, ms); });
})();
</script>';

$html = str_replace('</head>', $mobileFix . $filterCss . '</head>', $html);
$html = str_replace('</body>', $updateFilterScript . $filterScript . $cartSyncScript . $unfixScript . $heightReporterScript . $phoneFrScript . $frFixScript . $colorFixScript . '</body>', $html);

header('Content-Type: text/html; charset=utf-8');
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: payment=(self)');
echo $html;
