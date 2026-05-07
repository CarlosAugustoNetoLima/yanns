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
            <h2 class="boutique-heading" data-name="Paragraph container">
                <span class="boutique-heading-pink"><?= htmlspecialchars(I18n::t('boutique.heading_pink')) ?></span>
                <span class="boutique-heading-black"><?= htmlspecialchars(I18n::t('boutique.heading_black')) ?></span>
            </h2>
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
            <div class="boutique-carousel-viewport">
                <div class="boutique-carousel-track" data-boutique-track>
                    <?php foreach ($slides as $i => $src): ?>
                        <div class="boutique-carousel-slide" role="group" aria-roledescription="slide" aria-label="<?= ($i + 1) ?> / <?= count($slides) ?>">
                            <img
                                src="<?= htmlspecialchars($src) ?>"
                                alt="Coco Choco &amp; Toké Toké — <?= ($i + 1) ?>"
                                loading="<?= $i === 0 ? 'eager' : 'lazy' ?>"
                            >
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <button type="button" class="boutique-carousel-arrow boutique-carousel-arrow-prev" data-boutique-prev aria-label="Précédent">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
            <button type="button" class="boutique-carousel-arrow boutique-carousel-arrow-next" data-boutique-next aria-label="Suivant">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M9 6L15 12L9 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>

            <div class="boutique-carousel-dots" data-boutique-dots role="tablist">
                <?php foreach ($slides as $i => $src): ?>
                    <button
                        type="button"
                        class="boutique-carousel-dot<?= $i === 0 ? ' active' : '' ?>"
                        data-boutique-dot="<?= $i ?>"
                        role="tab"
                        aria-label="<?= ($i + 1) ?>"
                        aria-selected="<?= $i === 0 ? 'true' : 'false' ?>"
                    ></button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<script>
(function () {
    document.querySelectorAll('[data-boutique-carousel]').forEach(function (root) {
        const track = root.querySelector('[data-boutique-track]');
        const slides = root.querySelectorAll('.boutique-carousel-slide');
        const dots = root.querySelectorAll('[data-boutique-dot]');
        const prev = root.querySelector('[data-boutique-prev]');
        const next = root.querySelector('[data-boutique-next]');
        if (!track || slides.length === 0) return;

        let currentIndex = 0;
        let autoplayId = null;
        const AUTOPLAY_MS = 5000;

        function goTo(index, smooth = true) {
            index = (index + slides.length) % slides.length;
            currentIndex = index;
            const slide = slides[index];
            track.scrollTo({
                left: slide.offsetLeft - (track.clientWidth - slide.clientWidth) / 2,
                behavior: smooth ? 'smooth' : 'auto',
            });
            updateDots();
        }

        function updateDots() {
            dots.forEach(function (d, i) {
                const active = i === currentIndex;
                d.classList.toggle('active', active);
                d.setAttribute('aria-selected', active ? 'true' : 'false');
            });
        }

        function startAutoplay() {
            stopAutoplay();
            autoplayId = setInterval(function () { goTo(currentIndex + 1); }, AUTOPLAY_MS);
        }
        function stopAutoplay() {
            if (autoplayId) { clearInterval(autoplayId); autoplayId = null; }
        }

        if (prev) prev.addEventListener('click', function () { goTo(currentIndex - 1); stopAutoplay(); });
        if (next) next.addEventListener('click', function () { goTo(currentIndex + 1); stopAutoplay(); });
        dots.forEach(function (dot) {
            dot.addEventListener('click', function () {
                goTo(parseInt(dot.dataset.boutiqueDot, 10) || 0);
                stopAutoplay();
            });
        });

        // Sincroniza o índice quando o usuário arrasta/scrolla
        let scrollDebounce;
        track.addEventListener('scroll', function () {
            clearTimeout(scrollDebounce);
            scrollDebounce = setTimeout(function () {
                const center = track.scrollLeft + track.clientWidth / 2;
                let closest = 0;
                let minDist = Infinity;
                slides.forEach(function (s, i) {
                    const slideCenter = s.offsetLeft + s.clientWidth / 2;
                    const d = Math.abs(slideCenter - center);
                    if (d < minDist) { minDist = d; closest = i; }
                });
                if (closest !== currentIndex) {
                    currentIndex = closest;
                    updateDots();
                }
            }, 80);
        }, { passive: true });

        // Pausa autoplay no hover/focus
        root.addEventListener('mouseenter', stopAutoplay);
        root.addEventListener('mouseleave', startAutoplay);
        root.addEventListener('focusin', stopAutoplay);
        root.addEventListener('focusout', startAutoplay);

        // Pausa quando aba não está visível
        document.addEventListener('visibilitychange', function () {
            if (document.hidden) stopAutoplay(); else startAutoplay();
        });

        startAutoplay();
    });
})();
</script>
