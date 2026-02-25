<?php
namespace App\Views\Components;

/**
 * Dropdown Menu Item component
 * @param string $label Text label
 * @param string $href Link URL
 * @param string $state 'Default', 'Hover'
 */
$label = $label ?? 'Menu';
$href = $href ?? '#';
$state = $state ?? 'Default';

$baseClass = 'dropdown-menu-item';
if ($state === 'Hover') {
    $baseClass .= ' dropdown-menu-item-hover';
}
?>
<a href="<?= htmlspecialchars($href)?>" class="<?= $baseClass?>">
    <span class="dropdown-menu-item-text">
        <?= htmlspecialchars($label)?>
    </span>
</a>