<?php
use App\Helpers\Component;
use App\Helpers\I18n;

$slides = [
    '/assets/img/choco coco/1.jpeg',
    '/assets/img/choco coco/2.jpeg',
    '/assets/img/choco coco/3.jpeg',
    '/assets/img/choco coco/4.jpeg',
    '/assets/img/choco coco/5.jpeg',
];
?>
<section class="boutique-section" data-name="Boutique section" id="boutique">
    <div class="boutique-container" data-name="container">
        <div class="boutique-text" data-name="text">
            <h2 class="boutique-heading">
                <?= htmlspecialchars(I18n::t('boutique.heading_pink')) ?>
            </h2>
            <div class="boutique-logos" aria-hidden="true">
                <img src="/assets/img/coco choco.svg" alt="Coco Choco" class="boutique-logo boutique-logo-coco">
                <img src="/assets/img/toke toke.svg" alt="Toké Toké" class="boutique-logo boutique-logo-toke">
            </div>
            <div class="boutique-action">
                <?= Component::render('Button', [
                    'label' => I18n::t('boutique.discover'),
                    'variant' => 'primary',
                    'style' => 'solid',
                    'size' => 'extra-large',
                    'url' => '/collections',
                ])?>
            </div>
        </div>

        <div class="boutique-carousel" data-boutique-carousel aria-roledescription="carousel">
            <div class="boutique-carousel-thumbs" data-boutique-thumbs role="tablist" aria-orientation="vertical">
                <?php foreach ($slides as $i => $src): ?>
                    <button
                        type="button"
                        class="boutique-carousel-thumb<?= $i === 0 ? ' active' : '' ?>"
                        data-boutique-index="<?= $i ?>"
                        role="tab"
                        aria-label="<?= ($i + 1) ?> / <?= count($slides) ?>"
                        aria-selected="<?= $i === 0 ? 'true' : 'false' ?>"
                    >
                        <img src="<?= htmlspecialchars($src) ?>" alt="" loading="lazy">
                    </button>
                <?php endforeach; ?>
            </div>

            <div class="boutique-carousel-main" data-boutique-main>
                <?php foreach ($slides as $i => $src): ?>
                    <img
                        src="<?= htmlspecialchars($src) ?>"
                        alt="Coco Choco &amp; Toké Toké — <?= ($i + 1) ?>"
                        class="boutique-carousel-image<?= $i === 0 ? ' active' : '' ?>"
                        data-boutique-image="<?= $i ?>"
                        loading="<?= $i === 0 ? 'eager' : 'lazy' ?>"
                    >
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<script>
(function () {
    document.querySelectorAll('[data-boutique-carousel]').forEach(function (root) {
        const thumbs  = root.querySelectorAll('[data-boutique-index]');
        const images  = root.querySelectorAll('[data-boutique-image]');
        if (!thumbs.length || !images.length) return;

        let current = 0;
        let autoplayId = null;
        const AUTOPLAY_MS = 4500;

        function activate(index) {
            index = (index + thumbs.length) % thumbs.length;
            current = index;
            thumbs.forEach(function (t, i) {
                const active = i === index;
                t.classList.toggle('active', active);
                t.setAttribute('aria-selected', active ? 'true' : 'false');
            });
            images.forEach(function (img, i) {
                img.classList.toggle('active', i === index);
            });

            // Garante que o thumb ativo esteja visível dentro da coluna
            const activeThumb = thumbs[index];
            if (activeThumb && typeof activeThumb.scrollIntoView === 'function') {
                activeThumb.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'nearest' });
            }
        }

        thumbs.forEach(function (t) {
            t.addEventListener('click', function () {
                activate(parseInt(t.dataset.boutiqueIndex, 10) || 0);
                stopAutoplay();
            });
        });

        function startAutoplay() {
            stopAutoplay();
            autoplayId = setInterval(function () { activate(current + 1); }, AUTOPLAY_MS);
        }
        function stopAutoplay() {
            if (autoplayId) { clearInterval(autoplayId); autoplayId = null; }
        }

        root.addEventListener('mouseenter', stopAutoplay);
        root.addEventListener('mouseleave', startAutoplay);
        root.addEventListener('focusin', stopAutoplay);
        root.addEventListener('focusout', startAutoplay);

        document.addEventListener('visibilitychange', function () {
            if (document.hidden) stopAutoplay(); else startAutoplay();
        });

        startAutoplay();
    });
})();
</script>
