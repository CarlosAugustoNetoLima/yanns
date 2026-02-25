<?php
/**
 * Component: Tab
 * 
 * @param string $label The text to display
 * @param string $href The link URL (optional, defaults to #)
 * @param bool $active Whether the tab is in active/primary state
 * @param string $variant Variant name ('primary' or 'default')
 * @param string $class Additional CSS classes
 */
?>
<a href="<?= $href ?? '#'?>"
    class="tab <?=($active ?? false) ? 'active' : ''?> <?= isset($variant) ? 'tab-' . $variant : ''?> <?= $class ?? ''?>">
    <?= $label ?? 'HOME'?>
</a>