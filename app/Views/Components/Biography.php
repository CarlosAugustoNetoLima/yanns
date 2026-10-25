<?php
use App\Helpers\Component;
use App\Helpers\I18n;

// Indexes consumidos pelo template:
//   [0] hero collage grande (top-right)
//   [1] stage section (4:5 ao lado de "Sur scène")
//   [2] hero collage médio (bottom-left)
//   [4] hero collage pequeno (centro, deslocado)
$photos = [
    '/assets/img/yanns/NZR_3590.webp', // backstage portrait sorrindo, mic — dominante
    '/assets/img/yanns/NZR_3771.webp', // wide palco com público em luz vermelha — Sur scène
    '/assets/img/yanns/NZR_3630.webp', // palco em luz roxa, retrato cinemático
    '/assets/img/yanns/NZR_3645.webp', // (slot livre)
    '/assets/img/yanns/NZR_3605.webp', // backstage sentado nas escadas, contemplativo
];

$stats = [
    ['number' => I18n::t('bio.stat.streams'), 'label' => I18n::t('bio.stat.streams_label')],
    ['number' => I18n::t('bio.stat.albums'),  'label' => I18n::t('bio.stat.albums_label')],
    ['number' => I18n::t('bio.stat.tickets'), 'label' => I18n::t('bio.stat.tickets_label')],
    ['number' => I18n::t('bio.stat.views'),   'label' => I18n::t('bio.stat.views_label')],
    ['number' => I18n::t('bio.stat.shows'),   'label' => I18n::t('bio.stat.shows_label')],
];

$awards = [
    I18n::t('bio.award.diamond'),
    I18n::t('bio.award.w9'),
    I18n::t('bio.award.nrj'),
    I18n::t('bio.award.clip'),
];
?>

<section class="bio-page">

    <!-- HERO: nome gigante + intro + colagem de fotos -->
    <div class="bio-hero">
        <div class="bio-hero-bg" aria-hidden="true">
            <span class="bio-bg-logo"><?= \App\Helpers\IconHelper::logo(200, 'currentColor') ?></span>
        </div>

        <div class="bio-hero-inner">
            <div class="bio-hero-text">
                <span class="bio-eyebrow"><?= htmlspecialchars(I18n::t('bio.eyebrow')) ?></span>
                <h1 class="bio-hero-name">YANNS</h1>
                <p class="bio-hero-intro"><?= I18n::t('bio.intro') ?></p>
            </div>

            <div class="bio-hero-collage" aria-hidden="true">
                <div class="bio-collage-item bio-collage-item-1">
                    <img src="<?= htmlspecialchars($photos[0]) ?>" alt="">
                </div>
                <div class="bio-collage-item bio-collage-item-2">
                    <img src="<?= htmlspecialchars($photos[2]) ?>" alt="">
                </div>
                <div class="bio-collage-item bio-collage-item-3">
                    <img src="<?= htmlspecialchars($photos[4]) ?>" alt="">
                </div>
            </div>
        </div>
    </div>

    <!-- NÚMEROS -->
    <div class="bio-numbers">
        <div class="bio-numbers-inner">
            <div class="bio-numbers-head">
                <span class="bio-section-label"><?= htmlspecialchars(I18n::t('bio.numbers_title')) ?></span>
                <p class="bio-numbers-lead"><?= htmlspecialchars(I18n::t('bio.numbers_lead')) ?></p>
            </div>
            <div class="bio-stats-grid">
                <?php foreach ($stats as $stat): ?>
                    <div class="bio-stat">
                        <span class="bio-stat-number"><?= htmlspecialchars($stat['number']) ?></span>
                        <span class="bio-stat-label"><?= htmlspecialchars($stat['label']) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- DISTINÇÕES -->
    <div class="bio-awards">
        <div class="bio-awards-inner">
            <span class="bio-section-label"><?= htmlspecialchars(I18n::t('bio.awards_title')) ?></span>
            <ul class="bio-awards-list">
                <?php foreach ($awards as $i => $award): ?>
                    <li class="bio-award" style="--bio-award-delay: <?= $i * 0.08 ?>s;">
                        <span class="bio-award-number"><?= str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
                        <span class="bio-award-text"><?= htmlspecialchars($award) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <!-- SUR SCÈNE -->
    <div class="bio-stage">
        <div class="bio-stage-inner">
            <div class="bio-stage-photo">
                <img src="<?= htmlspecialchars($photos[1]) ?>" alt="">
            </div>
            <div class="bio-stage-text">
                <span class="bio-section-label"><?= htmlspecialchars(I18n::t('bio.stage_title')) ?></span>
                <p><?= I18n::t('bio.stage_text') ?></p>
            </div>
        </div>
    </div>

    <!-- 2026 — A SEGUIR + CTA -->
    <div class="bio-future">
        <div class="bio-future-inner">
            <span class="bio-section-label bio-section-label-light"><?= htmlspecialchars(I18n::t('bio.future_title')) ?></span>
            <p class="bio-future-text"><?= htmlspecialchars(I18n::t('bio.future_text')) ?></p>
            <div class="bio-future-cta">
                <?= Component::render('Button', [
                    'label'   => I18n::t('bio.cta'),
                    'variant' => 'primary',
                    'style'   => 'solid',
                    'size'    => 'extra-large',
                    'url'     => '/dates',
                ]) ?>
            </div>
        </div>
    </div>

</section>
