<?php
use App\Helpers\I18n;

$headerId = $id ?? uniqid('header_');
$activePage = $activePage ?? 'home';
$currentLang = I18n::current();
$languages = [
    'fr' => I18n::t('lang.fr'),
    'pt' => I18n::t('lang.pt'),
    'en' => I18n::t('lang.en'),
];
?>
<header class="header" id="<?= $headerId?>">
        <!-- Desktop Header -->
        <div class="header-inner header-desktop">
                <div class="header-socials">
                        <a href="https://www.instagram.com/yannsofficiel/" target="_blank">
                                <?=\App\Helpers\IconHelper::instagram(24, 'var(--color-neutral-600)')?>
                        </a>
                        <a href="https://www.tiktok.com/@yannsofficiel" target="_blank">
                                <?=\App\Helpers\IconHelper::tikTok(24, 'var(--color-neutral-600)')?>
                        </a>
                        <a href="https://www.youtube.com/channel/UCpbRjfY2hvgKJBJkOsvzuMw" target="_blank">
                                <?=\App\Helpers\IconHelper::youtube(24, 'var(--color-neutral-600)')?>
                        </a>
                        <a href="https://www.snapchat.com/@yannsofficiel" target="_blank">
                                <?=\App\Helpers\IconHelper::snapchat(24, 'var(--color-neutral-600)')?>
                        </a>
                </div>

                <div class="header-logo">
                        <?=\App\Helpers\IconHelper::logo(70, 'currentColor')?>
                </div>

                <div class="header-actions">
                        <button class="cart-icon-wrapper" data-cart-open aria-label="Panier">
                                <?= \App\Helpers\IconHelper::cartOutline(24, 'var(--color-neutral-600)') ?>
                                <span class="cart-badge hidden">0</span>
                        </button>
                        <div class="lang-switcher" data-lang-switcher>
                                <button
                                        type="button"
                                        class="lang-switcher-toggle icon-link"
                                        aria-haspopup="true"
                                        aria-expanded="false"
                                        aria-label="<?= htmlspecialchars(I18n::t('lang.label')) ?>"
                                >
                                        <?=\App\Helpers\IconHelper::web(24, 'var(--color-neutral-600)')?>
                                        <span class="lang-switcher-current"><?= strtoupper(htmlspecialchars($currentLang)) ?></span>
                                </button>
                                <ul class="lang-switcher-menu" role="menu">
                                        <?php foreach ($languages as $code => $label): ?>
                                                <li role="none">
                                                        <a
                                                                role="menuitem"
                                                                href="<?= htmlspecialchars(I18n::switchUrl($code)) ?>"
                                                                class="lang-switcher-item<?= $code === $currentLang ? ' active' : '' ?>"
                                                                hreflang="<?= htmlspecialchars($code) ?>"
                                                        >
                                                                <span class="lang-switcher-code"><?= strtoupper(htmlspecialchars($code)) ?></span>
                                                                <span class="lang-switcher-name"><?= htmlspecialchars($label) ?></span>
                                                        </a>
                                                </li>
                                        <?php endforeach; ?>
                                </ul>
                        </div>
                </div>
        </div>

        <!-- Desktop Navigation Menu -->
        <div class="header-navbar-desktop">
                <?=\App\Helpers\Component::render('Menu', ['activePage' => $activePage])?>
        </div>

        <!-- Mobile Header -->
        <div class="header-mobile">
                <?=\App\Helpers\IconHelper::logo(40, 'currentColor')?>
                <button class="header-menu-toggle" aria-label="Toggle Menu"
                        aria-controls="mobileMenuOverlay_<?= $headerId?>">
                        <!-- Animated Hamburger Lines -->
                        <span class="hamburger-line"></span>
                        <span class="hamburger-line"></span>
                        <span class="hamburger-line"></span>
                </button>
        </div>

        <!-- Mobile Overlay -->
        <div class="header-overlay" id="mobileMenuOverlay_<?= $headerId?>">
                <!-- Mobile Menu Component -->
                <?=\App\Helpers\Component::render('MobileMenu', ['activePage' => $activePage])?>
        </div>

        <script>
                (function () {
                        const header = document.getElementById('<?= $headerId?>');
                        if (!header) return;

                        const toggle = header.querySelector('.header-menu-toggle');
                        const overlay = document.getElementById('mobileMenuOverlay_<?= $headerId?>');

                        function toggleMenu() {
                                if (overlay && toggle) {
                                        overlay.classList.toggle('active');
                                        toggle.classList.toggle('active');
                                }
                        }

                        if (toggle) toggle.addEventListener('click', toggleMenu);

                        // Lang switcher dropdown (desktop)
                        const switcher = header.querySelector('[data-lang-switcher]');
                        if (switcher) {
                                const button = switcher.querySelector('.lang-switcher-toggle');
                                if (button) {
                                        button.addEventListener('click', function (e) {
                                                e.stopPropagation();
                                                const open = switcher.classList.toggle('open');
                                                button.setAttribute('aria-expanded', open ? 'true' : 'false');
                                        });
                                        document.addEventListener('click', function (e) {
                                                if (!switcher.contains(e.target)) {
                                                        switcher.classList.remove('open');
                                                        button.setAttribute('aria-expanded', 'false');
                                                }
                                        });
                                }
                        }
                })();
        </script>
</header>
