<?php
namespace App\Views\Components;

/**
 * Dropdown component
 * @param array $items Array of dropdown menu items (label, href, state)
 * @param string $class Additional CSS classes
 */
$items = $items ?? [];
$class = $class ?? '';
?>
<div class="dropdown <?= $class?>">
    <?php foreach ($items as $item): ?>
    <?=\App\Helpers\Component::render('DropdownMenuItem', $item)?>
    <?php
endforeach; ?>
</div>