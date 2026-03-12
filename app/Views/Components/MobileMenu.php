<?php
use App\Helpers\IconHelper;

$activePage = $activePage ?? 'home';
?>
<div class="mobile-menu">
    <a href="/" class="mobile-menu-item <?= $activePage === 'home' ? 'active' : ''?>">ACCUEIL</a>
    <a href="/musique" class="mobile-menu-item <?= $activePage === 'musique' ? 'active' : ''?>">MUSIQUE & VIDÉO</a>
    <a href="/dates" class="mobile-menu-item <?= $activePage === 'dates' ? 'active' : ''?>">DATES</a>
    <a href="/collections" class="mobile-menu-item <?= $activePage === 'collections' ? 'active' : ''?>">COLLECTIONS</a>
    <a href="/biographie" class="mobile-menu-item <?= $activePage === 'biographie' ? 'active' : ''?>">BIOGRAPHIE</a>

    <!-- Action Icons Row -->
    <div class="mobile-menu-actions">
        <a href="#" class="cart-icon-wrapper" data-cart-open target="_self">
            <?= IconHelper::cartOutline(24, 'var(--color-neutral-600)')?>
            <span class="cart-badge hidden">0</span>
        </a>
        <a href="#" target="_blank">
            <?= IconHelper::web(24, 'var(--color-neutral-600)')?>
        </a>
    </div>

    <!-- Social Icons Row -->
    <div class="mobile-menu-socials">
        <a href="https://www.instagram.com/yannsofficiel/" target="_blank">
            <?= IconHelper::instagram(24, 'var(--color-neutral-600)')?>
        </a>
        <a href="https://www.tiktok.com/@yannsofficiel" target="_blank">
            <?= IconHelper::tikTok(24, 'var(--color-neutral-600)')?>
        </a>
        <a href="https://www.youtube.com/channel/UCpbRjfY2hvgKJBJkOsvzuMw" target="_blank">
            <?= IconHelper::youtube(24, 'var(--color-neutral-600)')?>
        </a>
        <a href="https://www.snapchat.com/@yannszer" target="_blank">
            <?= IconHelper::snapchat(24, 'var(--color-neutral-600)')?>
        </a>
    </div>
</div>