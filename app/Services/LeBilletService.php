<?php

namespace App\Services;

class LeBilletService
{
    private string $checkoutBase = 'https://checkout.lebillet.eu/';
    private int    $cacheTtl     = 300;
    private string $cacheDir;
    private array  $productConfig;

    public function __construct()
    {
        $this->cacheDir = sys_get_temp_dir() . '/lebillet_cache_yanns/';
        if (!is_dir($this->cacheDir)) {
            @mkdir($this->cacheDir, 0755, true);
        }

        $configFile = __DIR__ . '/../Config/products.php';
        $this->productConfig = file_exists($configFile) ? require $configFile : [];
    }

    public function getProducts(int $eventId): array
    {
        $cacheKey = "products_{$eventId}";
        $cached   = $this->getCache($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        $sid = $this->fetchSid($eventId);
        if (!$sid) {
            return [];
        }

        $html = $this->fetchProductsHtml($eventId, $sid);
        if (!$html) {
            return [];
        }

        $products = $this->parseProducts($html);

        $this->setCache($cacheKey, $products);
        return $products;
    }

    public function getProduct(int $eventId, string $productId): ?array
    {
        foreach ($this->getProducts($eventId) as $p) {
            if ($p['id'] === $productId) {
                return $p;
            }
        }
        return null;
    }

    public function getCasquetteSibling(int $eventId, string $productId): ?array
    {
        $current = $this->getProduct($eventId, $productId);
        if (!$current || $current['type'] !== 'casquette') {
            return null;
        }
        foreach ($this->getProducts($eventId) as $p) {
            if ($p['type'] === 'casquette' && $p['id'] !== $productId) {
                return $p;
            }
        }
        return null;
    }

    private function fetchSid(int $eventId): ?string
    {
        $html = $this->curlGet($this->checkoutBase . $eventId);
        if (!$html) {
            return null;
        }
        if (preg_match('/name=["\']sid["\'][^>]*value=["\']([^"\']+)["\']/', $html, $m)) {
            return $m[1];
        }
        return null;
    }

    private function fetchProductsHtml(int $eventId, string $sid): ?string
    {
        $url = $this->checkoutBase . $eventId . '/products';
        return $this->curlPost($url, ['sid' => $sid]);
    }

    private function parseProducts(string $html): array
    {
        $pattern = '/id="product_(\d+)_(\d+)"/';
        if (!preg_match_all($pattern, $html, $matches, PREG_OFFSET_CAPTURE)) {
            return [];
        }

        $offsets    = $matches[0];
        $productIds = $matches[1];
        $variantIds = $matches[2];

        $products = [];

        for ($i = 0; $i < count($offsets); $i++) {
            $productId = $productIds[$i][0];
            $variantId = $variantIds[$i][0];

            $start = $i > 0 ? $offsets[$i - 1][1] : 0;
            $end   = $offsets[$i][1];
            $block = substr($html, $start, $end - $start);
            $clean = preg_replace('/\s+/', ' ', $block);

            $name        = $this->extractName($clean);
            $price       = $this->extractPrice($clean);
            $variantText = $this->extractVariantLabel($clean);

            if (!$name) {
                continue;
            }

            $nameLower = strtolower($name);
            if (str_contains($nameLower, 'promocional') ||
                str_contains($nameLower, 'voucher') ||
                str_contains($nameLower, 'coupon') ||
                str_contains($nameLower, 'código') ||
                str_contains($nameLower, 'code')) {
                continue;
            }

            $cfg = $this->productConfig[$productId] ?? [
                'image' => null,
                'type'  => 'accessory',
            ];

            if (!isset($products[$productId])) {
                $products[$productId] = [
                    'id'       => $productId,
                    'name'     => $name,
                    'price'    => $price,
                    'image'    => $cfg['image'],
                    'type'     => $cfg['type'],
                    'variants' => [],
                ];
            }

            $products[$productId]['variants'][] = [
                'id'    => $variantId,
                'label' => $this->cleanVariantLabel($variantText) ?? '',
            ];
        }

        return array_values($products);
    }

    private function extractName(string $block): ?string
    {
        if (preg_match('/<h5[^>]*>\s*(.+?)\s*<\/h5>/i', $block, $m)) {
            $name = trim(strip_tags($m[1]));
            $name = html_entity_decode($name, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            if (strlen($name) > 2) {
                return $name;
            }
        }
        return null;
    }

    private function extractPrice(string $block): ?float
    {
        if (preg_match('/€\s*([\d]+)[,.](\d{2})/', $block, $m)) {
            return (float) ($m[1] . '.' . $m[2]);
        }
        return null;
    }

    private function extractVariantLabel(string $block): ?string
    {
        if (preg_match_all('/<span[^>]*>\s*([^<]+?)\s*<\/span>/i', $block, $m)) {
            foreach ($m[1] as $candidate) {
                $candidate = trim(strip_tags($candidate));
                if ($candidate === '' || preg_match('/^\s*$/', $candidate)) {
                    continue;
                }
                if (str_starts_with($candidate, '€')) {
                    continue;
                }
                return $candidate;
            }
        }
        return null;
    }

    private function cleanVariantLabel(?string $raw): ?string
    {
        if ($raw === null || trim($raw) === '') {
            return null;
        }
        $label = trim($raw);
        $label = preg_replace('/^TAMANHO\s+/i', '', $label);
        $label = preg_replace('/^TAILLE\s+/i', '', $label);
        $label = preg_replace('/^SIZE\s+/i', '', $label);
        return $label !== '' ? $label : null;
    }

    private function curlGet(string $url): ?string
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_USERAGENT      => 'Mozilla/5.0 (compatible; Yanns/1.0)',
        ]);
        $result = curl_exec($ch);
        return ($result !== false && $result !== '') ? $result : null;
    }

    private function curlPost(string $url, array $fields = []): ?string
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($fields),
            CURLOPT_USERAGENT      => 'Mozilla/5.0 (compatible; Yanns/1.0)',
        ]);
        $result = curl_exec($ch);
        return ($result !== false && $result !== '') ? $result : null;
    }

    private function getCache(string $key): ?array
    {
        $file = $this->cacheDir . md5($key) . '.json';
        if (!file_exists($file)) {
            return null;
        }
        if (time() - filemtime($file) > $this->cacheTtl) {
            @unlink($file);
            return null;
        }
        $data = json_decode(file_get_contents($file), true);
        return is_array($data) ? $data : null;
    }

    private function setCache(string $key, array $data): void
    {
        $file = $this->cacheDir . md5($key) . '.json';
        file_put_contents($file, json_encode($data));
    }
}
