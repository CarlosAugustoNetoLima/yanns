<?php use App\Helpers\I18n; ?>
<footer class="footer">
    <div class="footer-inner">
        <div class="footer-top">
            <div class="footer-logo">
                <?= \App\Helpers\IconHelper::logo(40, 'currentColor') ?>
            </div>
            <nav class="footer-nav">
                <a href="/"><?= htmlspecialchars(I18n::t('nav.home')) ?></a>
                <a href="/musique"><?= htmlspecialchars(I18n::t('nav.music')) ?></a>
                <a href="/dates"><?= htmlspecialchars(I18n::t('nav.dates')) ?></a>
                <a href="/collections"><?= htmlspecialchars(I18n::t('nav.collections')) ?></a>
                <a href="/biographie"><?= htmlspecialchars(I18n::t('nav.bio')) ?></a>
            </nav>
        </div>

        <div class="footer-bottom">
            <p class="copyright"><?= htmlspecialchars(I18n::t('footer.copyright', ['year' => date('Y')])) ?></p>
            <div class="footer-legal">
                <a href="/mentions-legales"><?= htmlspecialchars(I18n::t('footer.legal')) ?></a>
                <a href="/politique-de-confidentialite"><?= htmlspecialchars(I18n::t('footer.privacy')) ?></a>
                <a href="/cgu"><?= htmlspecialchars(I18n::t('footer.cgu')) ?></a>
                <a href="/cookies"><?= htmlspecialchars(I18n::t('footer.cookies')) ?></a>
            </div>
        </div>
    </div>
</footer>
