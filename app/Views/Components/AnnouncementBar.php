<?php

use App\Helpers\I18n;

/**
 * Faixa de novidades (marquee) — anuncia as novas coleções acima do hero.
 * A faixa inteira é um CTA clicável que leva à página do produto.
 *
 * O texto corre em loop contínuo: o conteúdo é duplicado em dois grupos
 * idênticos e a animação CSS desloca a pista em -50% (ver announcement-bar.css).
 */

$announcementUrl  = $announcementUrl ?? '/produto?id=12616';
$announcementLogo = $announcementLogo ?? '/assets/img/collections/LE MAILLOT.png';

$text = I18n::t('announcement.text');
$cta  = I18n::t('announcement.cta');

// Unidade repetida da pista: texto + logo Le Maillot + CTA, separados por estrela.
$unit = '<span class="announcement-bar-text">' . htmlspecialchars($text) . '</span>'
      . '<img class="announcement-bar-logo" src="' . htmlspecialchars($announcementLogo) . '" alt="Le Maillot">'
      . '<span class="announcement-bar-cta">' . htmlspecialchars($cta) . '</span>'
      . '<span class="announcement-bar-star" aria-hidden="true">&#10022;</span>';

$group = str_repeat($unit, 8);
?>
<a class="announcement-bar" href="<?= htmlspecialchars($announcementUrl) ?>"
   aria-label="<?= htmlspecialchars($text . ' — Le Maillot — ' . $cta) ?>">
    <div class="announcement-bar-track">
        <div class="announcement-bar-group"><?= $group ?></div>
        <div class="announcement-bar-group" aria-hidden="true"><?= $group ?></div>
    </div>
</a>
