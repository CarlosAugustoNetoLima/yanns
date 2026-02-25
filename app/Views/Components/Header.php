<?php
// Generate unique ID for this instance if not provided
$headerId = $id ?? uniqid('header_');
$activePage = $activePage ?? 'home';
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
                        <a href="#" class="icon-link">
                                <?=\App\Helpers\IconHelper::cartOutline(24, 'var(--color-neutral-600)')?>
                        </a>
                        <a href="#" class="icon-link">
                                <?=\App\Helpers\IconHelper::web(24, 'var(--color-neutral-600)')?>
                        </a>
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
                })();
        </script>
</header>