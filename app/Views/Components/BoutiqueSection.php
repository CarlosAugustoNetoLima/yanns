<?php
use App\Helpers\Component;
use App\Helpers\I18n;

$slides = [
    '/assets/img/choco coco/1.jpeg',
    '/assets/img/choco coco/2.jpeg',
    '/assets/img/choco coco/3.jpeg',
    '/assets/img/choco coco/4.jpeg',
    '/assets/img/choco coco/5.jpeg',
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
            <div class="boutique-carousel-thumbs-wrapper">
                <button type="button" class="boutique-carousel-thumbs-arrow boutique-carousel-thumbs-arrow-up" data-thumbs-up aria-label="Précédent" hidden>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M6 15L12 9L18 15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
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
                <button type="button" class="boutique-carousel-thumbs-arrow boutique-carousel-thumbs-arrow-down" data-thumbs-down aria-label="Suivant" hidden>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
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
        const thumbs    = root.querySelectorAll('[data-boutique-index]');
        const images    = root.querySelectorAll('[data-boutique-image]');
        const thumbsCol = root.querySelector('[data-boutique-thumbs]');
        const upBtn     = root.querySelector('[data-thumbs-up]');
        const downBtn   = root.querySelector('[data-thumbs-down]');
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

            // Garante que o thumb ativo esteja visível APENAS dentro da coluna,
            // sem scrollar a página (scrollIntoView nativo bubbla pra window)
            scrollThumbIntoView(thumbs[index]);
        }

        function scrollThumbIntoView(thumb) {
            if (!thumbsCol || !thumb) return;
            // Em layout horizontal (mobile), trabalha em scrollLeft; senão scrollTop
            const horizontal = getComputedStyle(thumbsCol).flexDirection === 'row';
            if (horizontal) {
                const ctLeft = thumbsCol.scrollLeft;
                const ctRight = ctLeft + thumbsCol.clientWidth;
                const tLeft = thumb.offsetLeft;
                const tRight = tLeft + thumb.offsetWidth;
                if (tLeft < ctLeft) {
                    thumbsCol.scrollTo({ left: tLeft, behavior: 'smooth' });
                } else if (tRight > ctRight) {
                    thumbsCol.scrollTo({ left: tRight - thumbsCol.clientWidth, behavior: 'smooth' });
                }
            } else {
                const ctTop = thumbsCol.scrollTop;
                const ctBottom = ctTop + thumbsCol.clientHeight;
                const tTop = thumb.offsetTop;
                const tBottom = tTop + thumb.offsetHeight;
                if (tTop < ctTop) {
                    thumbsCol.scrollTo({ top: tTop, behavior: 'smooth' });
                } else if (tBottom > ctBottom) {
                    thumbsCol.scrollTo({ top: tBottom - thumbsCol.clientHeight, behavior: 'smooth' });
                }
            }
        }

        thumbs.forEach(function (t) {
            t.addEventListener('click', function () {
                activate(parseInt(t.dataset.boutiqueIndex, 10) || 0);
                stopAutoplay();
            });
        });

        // Setas de scroll vertical da coluna de thumbs (não muda o slide ativo)
        function thumbStep() {
            const first = thumbs[0];
            if (!first) return 80;
            const styles = getComputedStyle(thumbsCol);
            const gap = parseInt(styles.rowGap || styles.gap || '0', 10) || 0;
            return first.offsetHeight + gap;
        }

        function isOverflowing() {
            return thumbsCol && thumbsCol.scrollHeight > thumbsCol.clientHeight + 1;
        }

        function updateArrows() {
            if (!thumbsCol) return;
            const overflow = isOverflowing();
            if (!overflow) {
                if (upBtn) upBtn.hidden = true;
                if (downBtn) downBtn.hidden = true;
                return;
            }
            const max = thumbsCol.scrollHeight - thumbsCol.clientHeight;
            const top = thumbsCol.scrollTop;
            if (upBtn) upBtn.hidden = top <= 1;
            if (downBtn) downBtn.hidden = top >= max - 1;
        }

        if (upBtn) {
            upBtn.addEventListener('click', function () {
                thumbsCol.scrollBy({ top: -thumbStep() * 2, behavior: 'smooth' });
                stopAutoplay();
            });
        }
        if (downBtn) {
            downBtn.addEventListener('click', function () {
                thumbsCol.scrollBy({ top: thumbStep() * 2, behavior: 'smooth' });
                stopAutoplay();
            });
        }
        if (thumbsCol) {
            thumbsCol.addEventListener('scroll', updateArrows, { passive: true });
            window.addEventListener('resize', updateArrows);
            // Avalia depois da renderização inicial
            requestAnimationFrame(updateArrows);
            // E novamente quando as imagens carregarem (scrollHeight pode mudar)
            thumbsCol.querySelectorAll('img').forEach(function (img) {
                if (!img.complete) img.addEventListener('load', updateArrows, { once: true });
            });
        }

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
