<?php
use App\Helpers\Component;
use App\Helpers\I18n;

$activePage = $activePage ?? 'home';
?>
<nav class="menu">
    <div class="menu-spacer"></div>
    <?= Component::render('MenuItem', ['label' => I18n::t('nav.home'),        'active' => $activePage === 'home',        'href' => '/'])?>
    <?= Component::render('MenuItem', ['label' => I18n::t('nav.music'),       'active' => $activePage === 'musique',     'href' => '/musique'])?>
    <?= Component::render('MenuItem', ['label' => I18n::t('nav.dates'),       'active' => $activePage === 'dates',       'href' => '/dates'])?>
    <?= Component::render('MenuItem', ['label' => I18n::t('nav.collections'), 'active' => $activePage === 'collections', 'href' => '/collections'])?>
    <?= Component::render('MenuItem', ['label' => I18n::t('nav.bio'),         'active' => $activePage === 'biographie',  'href' => '/biographie'])?>
    <div class="menu-spacer"></div>
</nav>
