<?php
use App\Helpers\Component;
use App\Helpers\I18n;

/**
 * Coleções da home — organizadas em 3 abas (tabs).
 * Cada coleção tem o seu conjunto de imagens e o productId do produto associado.
 * O autoplay percorre as imagens da coleção ativa; ao terminar, a barra de
 * progresso reinicia e avança automaticamente para a coleção seguinte.
 */
$collections = [
    // Le Maillot descontinuado — comentado para reutilizar depois
    // [
    //     'key'       => 'maillot',
    //     'label'     => 'LE MAILLOT',
    //     'productId' => '12616',
    //     'slides'    => [
    //         '/assets/img/yanns/gallery/1.webp',
    //         '/assets/img/yanns/gallery/2.webp',
    //         '/assets/img/yanns/gallery/3.webp',
    //         '/assets/img/yanns/gallery/4.webp',
    //         '/assets/img/yanns/gallery/5.webp',
    //         '/assets/img/yanns/gallery/6.webp',
    //         '/assets/img/yanns/gallery/7.webp',
    //         '/assets/img/yanns/gallery/8.webp',
    //         '/assets/img/yanns/gallery/9.webp',
    //         '/assets/img/yanns/gallery/10.webp',
    //         '/assets/img/yanns/gallery/11.webp',
    //         '/assets/img/yanns/gallery/12.webp',
    //         '/assets/img/yanns/gallery/13.webp',
    //         '/assets/img/yanns/gallery/14.webp',
    //         '/assets/img/yanns/gallery/15.webp',
    //         '/assets/img/yanns/gallery/16.webp',
    //         '/assets/img/yanns/gallery/17.webp',
    //         '/assets/img/yanns/gallery/18.webp',
    //         '/assets/img/yanns/gallery/19.webp',
    //         '/assets/img/yanns/gallery/20.webp',
    //         '/assets/img/yanns/gallery/21.webp',
    //         '/assets/img/yanns/gallery/22.webp',
    //         '/assets/img/yanns/gallery/23.webp',
    //     ],
    // ],
    [
        'key'       => 'coco',
        'label'     => 'COCO CHOCO',
        'productUrl' => '/collections',
        'slides'    => [
            '/assets/img/collections/choco coco/1.webp',
            '/assets/img/collections/choco coco/2.webp',
            '/assets/img/collections/choco coco/3.webp',
            '/assets/img/collections/choco coco/4.webp',
            '/assets/img/collections/choco coco/5.webp',
            '/assets/img/collections/choco coco/NZR_3430.webp',
            '/assets/img/yanns/gallery/32.webp',
            '/assets/img/yanns/gallery/33.webp',
            '/assets/img/yanns/gallery/35.webp',
        ],
    ],
    [
        'key'       => 'toke',
        'label'     => 'TOKÉ TOKÉ',
        'productUrl' => '/collections',
        'slides'    => [
            '/assets/img/collections/toke toke/NZR_3449.webp',
            '/assets/img/collections/toke toke/NZR_3470.webp',
            '/assets/img/yanns/gallery/25.webp',
            '/assets/img/yanns/gallery/27.webp',
            '/assets/img/yanns/gallery/28.webp',
            '/assets/img/yanns/gallery/29.webp',
            '/assets/img/yanns/gallery/39.webp',
        ],
    ],
];
?>
<section class="boutique-section" data-name="Boutique section" id="boutique">
    <div class="boutique-container" data-name="container">
        <div class="boutique-text" data-name="text">
            <h2 class="boutique-heading">
                <?= htmlspecialchars(I18n::t('boutique.heading_pink')) ?>
            </h2>
            <div class="boutique-logos" aria-hidden="true">
                <!-- Le Maillot descontinuado — comentado para reutilizar depois
                <img src="/assets/img/collections/LE MAILLOT.png" alt="Le Maillot" class="boutique-logo boutique-logo-maillot">
                -->
                <img src="/assets/img/collections/choco coco/coco choco.svg" alt="Coco Choco" class="boutique-logo boutique-logo-coco">
                <img src="/assets/img/collections/toke toke/toke toke.svg" alt="Toké Toké" class="boutique-logo boutique-logo-toke">
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
            <!-- Tabs das coleções -->
            <div class="boutique-carousel-tabs" role="tablist" aria-label="Collections">
                <?php foreach ($collections as $c => $col): ?>
                    <button
                        type="button"
                        class="boutique-tab<?= $c === 0 ? ' active' : '' ?>"
                        data-boutique-tab="<?= $c ?>"
                        role="tab"
                        aria-selected="<?= $c === 0 ? 'true' : 'false' ?>"
                    ><?= htmlspecialchars($col['label']) ?></button>
                <?php endforeach; ?>
            </div>

            <!-- Loader / barra de progresso -->
            <div class="boutique-carousel-loader" aria-hidden="true">
                <span class="boutique-carousel-loader-bar" data-boutique-loader></span>
            </div>

            <div class="boutique-carousel-body">
                <div class="boutique-carousel-thumbs-wrapper">
                    <button type="button" class="boutique-carousel-thumbs-arrow boutique-carousel-thumbs-arrow-up" data-thumbs-up aria-label="Précédent" hidden>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M6 15L12 9L18 15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    <div class="boutique-carousel-thumbs" data-boutique-thumbs role="tablist" aria-orientation="vertical">
                        <?php foreach ($collections as $c => $col): ?>
                            <?php foreach ($col['slides'] as $i => $src): ?>
                                <button
                                    type="button"
                                    class="boutique-carousel-thumb<?= ($c === 0 && $i === 0) ? ' active' : '' ?>"
                                    data-boutique-col="<?= $c ?>"
                                    data-boutique-index="<?= $i ?>"
                                    role="tab"
                                    aria-label="<?= htmlspecialchars($col['label']) ?> — <?= ($i + 1) ?> / <?= count($col['slides']) ?>"
                                    aria-selected="<?= ($c === 0 && $i === 0) ? 'true' : 'false' ?>"
                                    <?= $c === 0 ? '' : 'hidden' ?>
                                >
                                    <img src="<?= htmlspecialchars($src) ?>" alt="" loading="lazy">
                                </button>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="boutique-carousel-thumbs-arrow boutique-carousel-thumbs-arrow-down" data-thumbs-down aria-label="Suivant" hidden>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>

                <div class="boutique-carousel-main" data-boutique-main>
                    <?php foreach ($collections as $c => $col): ?>
                        <?php foreach ($col['slides'] as $i => $src): ?>
                            <div
                                class="boutique-carousel-image<?= ($c === 0 && $i === 0) ? ' active' : '' ?>"
                                data-boutique-col="<?= $c ?>"
                                data-boutique-image="<?= $i ?>"
                                <?= $c === 0 ? '' : 'hidden' ?>
                            >
                                <img
                                    src="<?= htmlspecialchars($src) ?>"
                                    alt=""
                                    class="boutique-carousel-bg"
                                    aria-hidden="true"
                                    loading="<?= ($c === 0 && $i === 0) ? 'eager' : 'lazy' ?>"
                                >
                                <a href="<?= htmlspecialchars($col['productUrl']) ?>"
                                   class="boutique-carousel-fg-link"
                                   aria-label="Voir la collection">
                                    <img
                                        src="<?= htmlspecialchars($src) ?>"
                                        alt="<?= htmlspecialchars($col['label']) ?> — <?= ($i + 1) ?>"
                                        class="boutique-carousel-fg"
                                        loading="<?= ($c === 0 && $i === 0) ? 'eager' : 'lazy' ?>"
                                    >
                                    <span class="boutique-carousel-shop-icon" aria-hidden="true">
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                                            <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            <line x1="3" y1="6" x2="21" y2="6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                            <path d="M16 10a4 4 0 01-8 0" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
(function () {
    document.querySelectorAll('[data-boutique-carousel]').forEach(function (root) {
        const tabs      = Array.from(root.querySelectorAll('[data-boutique-tab]'));
        const thumbs    = Array.from(root.querySelectorAll('[data-boutique-col][data-boutique-index]'));
        const images    = Array.from(root.querySelectorAll('[data-boutique-col][data-boutique-image]'));
        const thumbsCol = root.querySelector('[data-boutique-thumbs]');
        const loaderBar = root.querySelector('[data-boutique-loader]');
        const upBtn     = root.querySelector('[data-thumbs-up]');
        const downBtn   = root.querySelector('[data-thumbs-down]');
        if (!tabs.length || !thumbs.length || !images.length) return;

        // Conta de imagens por coleção
        const colCounts = tabs.map(function (_, c) {
            return images.filter(function (img) { return +img.dataset.boutiqueCol === c; }).length;
        });

        let currentCol = 0;
        let currentIdx = 0;
        let autoplayId = null;
        const SLIDE_MS = 3000;

        function thumbFor(col, idx) {
            return thumbs.find(function (t) {
                return +t.dataset.boutiqueCol === col && +t.dataset.boutiqueIndex === idx;
            });
        }
        function imageFor(col, idx) {
            return images.find(function (im) {
                return +im.dataset.boutiqueCol === col && +im.dataset.boutiqueImage === idx;
            });
        }

        // Mostra/esconde thumbs e imagens conforme a coleção ativa
        function applyCollectionVisibility(col) {
            thumbs.forEach(function (t) {
                const show = +t.dataset.boutiqueCol === col;
                t.hidden = !show;
            });
            images.forEach(function (im) {
                const show = +im.dataset.boutiqueCol === col;
                im.hidden = !show;
                if (!show) im.classList.remove('active');
            });
            tabs.forEach(function (tab, c) {
                const active = c === col;
                tab.classList.toggle('active', active);
                tab.setAttribute('aria-selected', active ? 'true' : 'false');
            });
            if (thumbsCol) thumbsCol.scrollTo({ top: 0 });
        }

        // Atualiza a barra de progresso. animate=false aplica instantâneo (reset).
        function setLoader(ratio, animate) {
            if (!loaderBar) return;
            loaderBar.style.transition = animate ? 'width ' + SLIDE_MS + 'ms linear' : 'none';
            loaderBar.style.width = (ratio * 100) + '%';
        }

        function selectImage(idx) {
            currentIdx = idx;
            const total = colCounts[currentCol] || 1;
            thumbs.forEach(function (t) {
                const active = +t.dataset.boutiqueCol === currentCol && +t.dataset.boutiqueIndex === idx;
                t.classList.toggle('active', active);
                t.setAttribute('aria-selected', active ? 'true' : 'false');
            });
            images.forEach(function (im) {
                im.classList.toggle('active', +im.dataset.boutiqueCol === currentCol && +im.dataset.boutiqueImage === idx);
            });
            scrollThumbIntoView(thumbFor(currentCol, idx));
            // Barra preenche progressivamente até ao fim da coleção
            setLoader((idx + 1) / total, true);
        }

        function showCollection(col, opts) {
            opts = opts || {};
            currentCol = (col + tabs.length) % tabs.length;
            currentIdx = 0;
            applyCollectionVisibility(currentCol);
            setLoader(0, false);                 // reinicia a barra
            requestAnimationFrame(function () {
                requestAnimationFrame(function () { selectImage(0); });
            });
            requestAnimationFrame(updateArrows);
        }

        function scrollThumbIntoView(thumb) {
            if (!thumbsCol || !thumb) return;
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

        // Clique nas tabs
        tabs.forEach(function (tab, c) {
            tab.addEventListener('click', function () {
                showCollection(c);
                startAutoplay();
            });
        });

        // Clique nas thumbs
        thumbs.forEach(function (t) {
            t.addEventListener('click', function () {
                const col = +t.dataset.boutiqueCol;
                const idx = +t.dataset.boutiqueIndex;
                if (col !== currentCol) { showCollection(col); }
                selectImage(idx);
                stopAutoplay();
            });
        });

        function thumbStep() {
            const first = thumbs.find(function (t) { return !t.hidden; });
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
            requestAnimationFrame(updateArrows);
            thumbsCol.querySelectorAll('img').forEach(function (img) {
                if (!img.complete) img.addEventListener('load', updateArrows, { once: true });
            });
        }

        function tick() {
            const total = colCounts[currentCol] || 1;
            if (currentIdx + 1 < total) {
                selectImage(currentIdx + 1);
            } else {
                // Terminou a coleção → próxima coleção, reinicia a barra
                showCollection(currentCol + 1);
            }
        }

        function startAutoplay() {
            stopAutoplay();
            autoplayId = setInterval(tick, SLIDE_MS);
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

        // Estado inicial
        showCollection(0);
        startAutoplay();
    });
})();
</script>
