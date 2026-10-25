<a href="<?= $href ?? '#'?>" class="menu-item <?=($active ?? false) ? 'active' : ''?> <?= $class ?? ''?>"<?= isset($target) ? ' target="'.htmlspecialchars($target).'" rel="noopener noreferrer"' : '' ?>>
    <?= $label ?? 'HOME'?>
</a>