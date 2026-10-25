<?php
use App\Helpers\IconHelper;
use App\Helpers\I18n;

$activePage = $activePage ?? 'home';
$currentLang = I18n::current();
$languages = [
    'fr' => I18n::t('lang.fr'),
    'pt' => I18n::t('lang.pt'),
    'en' => I18n::t('lang.en'),
];
?>
<div class="mobile-menu">
    <a href="/" class="mobile-menu-item <?= $activePage === 'home' ? 'active' : ''?>"><?= htmlspecialchars(I18n::t('nav.home')) ?></a>
    <a href="/musique" class="mobile-menu-item <?= $activePage === 'musique' ? 'active' : ''?>"><?= htmlspecialchars(I18n::t('nav.music')) ?></a>
    <a href="/dates" class="mobile-menu-item <?= $activePage === 'dates' ? 'active' : ''?>"><?= htmlspecialchars(I18n::t('nav.dates')) ?></a>
    <a href="https://yannsmusic.store/" target="_blank" rel="noopener noreferrer" class="mobile-menu-item"><?= htmlspecialchars(I18n::t('nav.collections')) ?></a>
    <a href="/biographie" class="mobile-menu-item <?= $activePage === 'biographie' ? 'active' : ''?>"><?= htmlspecialchars(I18n::t('nav.bio')) ?></a>

    <!-- Language switcher (mobile) -->
    <div class="mobile-menu-lang" role="group" aria-label="<?= htmlspecialchars(I18n::t('lang.label')) ?>">
        <span class="mobile-menu-lang-icon" aria-hidden="true">
            <?= IconHelper::web(20, 'var(--color-neutral-600)')?>
        </span>
        <?php foreach ($languages as $code => $label): ?>
            <a
                href="<?= htmlspecialchars(I18n::switchUrl($code)) ?>"
                class="mobile-menu-lang-item<?= $code === $currentLang ? ' active' : '' ?>"
                hreflang="<?= htmlspecialchars($code) ?>"
                aria-current="<?= $code === $currentLang ? 'true' : 'false' ?>"
            ><?= strtoupper(htmlspecialchars($code)) ?></a>
        <?php endforeach; ?>
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
