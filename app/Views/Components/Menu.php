<?php
use App\Helpers\Component;

$activePage = $activePage ?? 'home';
?>
<nav class="menu">
    <div class="menu-spacer"></div>
    <?= Component::render('MenuItem', ['label' => 'ACCUEIL', 'active' => $activePage === 'home', 'href' => '/'])?>
    <?= Component::render('MenuItem', ['label' => 'MUSIQUE & VIDÉO', 'active' => $activePage === 'musique', 'href' => '/musique'])?>
    <?= Component::render('MenuItem', ['label' => 'DATES', 'active' => $activePage === 'dates', 'href' => '/dates'])?>
    <?= Component::render('MenuItem', ['label' => 'COLLECTIONS', 'active' => $activePage === 'collections', 'href' => '/collections'])?>
    <?= Component::render('MenuItem', ['label' => 'BIOGRAPHIE', 'active' => $activePage === 'biographie', 'href' => '/biographie'])?>
    <div class="menu-spacer"></div>
</nav>