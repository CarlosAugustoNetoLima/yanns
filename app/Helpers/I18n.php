<?php

namespace App\Helpers;

/**
 * I18n — gestão de idioma e tradução de strings.
 *
 * Idiomas suportados: fr (francês, padrão), pt (português PT), en (inglês).
 *
 * Prioridade de detecção (em init()):
 *   1. Query string ?lang=XX  → seta cookie + (chamador deve redirecionar)
 *   2. Cookie yanns_lang
 *   3. Header Accept-Language do browser
 *   4. Fallback: 'fr'
 */
class I18n
{
    public const COOKIE_NAME    = 'yanns_lang';
    public const COOKIE_TTL     = 60 * 60 * 24 * 365; // 1 ano
    public const DEFAULT_LANG   = 'fr';
    public const SUPPORTED      = ['fr', 'pt', 'en'];

    private static string $current = self::DEFAULT_LANG;
    private static array  $translations = [];
    private static bool   $booted = false;

    /**
     * Inicializa: resolve idioma, define cookie se aplicável, carrega traduções.
     * Pode ser chamado várias vezes (idempotente).
     *
     * @return string|null  Se houve troca via ?lang=, retorna o idioma novo
     *                      para o chamador redirecionar. Null caso contrário.
     */
    public static function init(): ?string
    {
        if (self::$booted) {
            return null;
        }

        $switched = null;

        // 1. ?lang=XX
        if (isset($_GET['lang'])) {
            $candidate = strtolower(trim($_GET['lang']));
            if (self::isSupported($candidate)) {
                self::setCookie($candidate);
                self::$current = $candidate;
                $switched = $candidate;
            }
        }

        if ($switched === null) {
            // 2. Cookie
            if (isset($_COOKIE[self::COOKIE_NAME]) && self::isSupported($_COOKIE[self::COOKIE_NAME])) {
                self::$current = $_COOKIE[self::COOKIE_NAME];
            } else {
                // 3. Accept-Language
                $detected = self::detectFromAcceptLanguage($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '');
                self::$current = $detected ?? self::DEFAULT_LANG;
            }
        }

        self::loadTranslations(self::$current);
        self::$booted = true;

        return $switched;
    }

    public static function current(): string
    {
        if (!self::$booted) self::init();
        return self::$current;
    }

    public static function available(): array
    {
        return self::SUPPORTED;
    }

    public static function isSupported(string $lang): bool
    {
        return in_array($lang, self::SUPPORTED, true);
    }

    /**
     * Traduz uma chave. Se não existir no idioma atual, tenta o default;
     * se também não existir, retorna a própria chave.
     *
     * Suporta interpolação simples: t('foo.bar', ['name' => 'X']) substitui {name}.
     */
    public static function t(string $key, array $params = []): string
    {
        if (!self::$booted) self::init();

        $value = self::$translations[$key] ?? null;

        if ($value === null && self::$current !== self::DEFAULT_LANG) {
            // Carrega o default uma vez como fallback
            static $fallbackLoaded = false;
            static $fallback = [];
            if (!$fallbackLoaded) {
                $file = __DIR__ . '/../Config/lang/' . self::DEFAULT_LANG . '.php';
                $fallback = file_exists($file) ? require $file : [];
                $fallbackLoaded = true;
            }
            $value = $fallback[$key] ?? null;
        }

        if ($value === null) {
            $value = $key; // último fallback
        }

        if (!empty($params)) {
            foreach ($params as $k => $v) {
                $value = str_replace('{' . $k . '}', (string) $v, $value);
            }
        }

        return $value;
    }

    /**
     * Formata um número de visualizações conforme o idioma actual.
     *   FR: 1500 → "1,5 k"; 1_600_000 → "1,6 M"
     *   PT: 1500 → "1,5 mil"; 1_600_000 → "1,6 mi"
     *   EN: 1500 → "1.5K"; 1_600_000 → "1.6M"
     */
    public static function formatViews(int $count): string
    {
        if (!self::$booted) self::init();
        $lang = self::$current;

        $format = function (float $val) use ($lang): string {
            // 1 casa decimal, sem zero à direita
            $s = number_format($val, 1, '.', '');
            if (substr($s, -2) === '.0') $s = substr($s, 0, -2);
            // Vírgula como decimal em FR/PT
            if ($lang !== 'en') $s = str_replace('.', ',', $s);
            return $s;
        };

        if ($count >= 1_000_000) {
            $v = $format($count / 1_000_000);
            return match ($lang) {
                'en' => $v . 'M',
                'pt' => $v . ' mi',
                default => $v . ' M',
            };
        }
        if ($count >= 1_000) {
            $v = $format($count / 1_000);
            return match ($lang) {
                'en' => $v . 'K',
                'pt' => $v . ' mil',
                default => $v . ' k',
            };
        }
        return (string) $count;
    }

    /**
     * Formata "tempo atrás" plural-aware: (4, 'year') → "il y a 4 ans" / "há 4 anos" / "4 years ago"
     */
    public static function formatTimeAgo(int $value, string $unit): string
    {
        if (!self::$booted) self::init();
        $key = 'videos.time.' . $unit . ($value > 1 ? 's' : '');
        $unitText = self::t($key);
        return self::t('videos.time_ago', ['n' => $value, 'unit' => $unitText]);
    }

    private static function loadTranslations(string $lang): void
    {
        $file = __DIR__ . '/../Config/lang/' . $lang . '.php';
        self::$translations = file_exists($file) ? require $file : [];
    }

    private static function setCookie(string $lang): void
    {
        if (headers_sent()) return;
        setcookie(
            self::COOKIE_NAME,
            $lang,
            [
                'expires'  => time() + self::COOKIE_TTL,
                'path'     => '/',
                'samesite' => 'Lax',
                'httponly' => false,
            ]
        );
        $_COOKIE[self::COOKIE_NAME] = $lang;
    }

    private static function detectFromAcceptLanguage(string $header): ?string
    {
        if ($header === '') return null;

        $entries = [];
        foreach (explode(',', $header) as $part) {
            $part = trim($part);
            if ($part === '') continue;
            $bits = explode(';', $part);
            $code = strtolower(trim($bits[0]));
            $q = 1.0;
            foreach (array_slice($bits, 1) as $b) {
                if (preg_match('/^q\s*=\s*([\d.]+)/i', trim($b), $m)) {
                    $q = (float) $m[1];
                }
            }
            $entries[] = ['code' => $code, 'q' => $q];
        }
        usort($entries, fn($a, $b) => $b['q'] <=> $a['q']);

        foreach ($entries as $e) {
            // pt-PT, pt-BR, pt → pt
            $base = explode('-', $e['code'])[0];
            if (self::isSupported($base)) return $base;
        }
        return null;
    }
}
