<?php
// Normalize props to lowercase for class mapping
$style = strtolower($style ?? 'solid');
$variant = strtolower($variant ?? 'primary');
$size = str_replace(' ', '-', strtolower($size ?? 'medium'));
$state = strtolower($state ?? 'default');
$icon = $icon ?? null;
$idEvent = $idEvent ?? null;
$event = $event ?? '';
$checkoutUrl = $checkoutUrl ?? 'https://checkout.lebillet.eu/';
$url = $url ?? '/';

$classes = [
    'btn',
    "btn-style-$style",
    "btn-variant-$variant",
    "btn-size-$size",
    $state !== 'default' ? "btn-state-$state" : '',
    $icon ? 'btn-icon' : '',
    $class ?? ''
];

$isDisabled = ($state === 'disabled' || (isset($disabled) && $disabled));
?>
<?php if (isset($idEvent)) { ?>
    <button onclick="openCustomModal('<?php echo $event; ?>', '<?php echo $checkoutUrl . $idEvent; ?>')" <?= isset($id) ? 'id="' . htmlspecialchars($id) . '"' : '' ?>     <?= isset($ariaLabel) ? 'aria-label="' . htmlspecialchars($ariaLabel) . '"' : '' ?> class="
    <?= implode(' ', array_filter($classes)) ?>" <?= $isDisabled ? 'disabled' : '' ?>>
        <?php if ($icon && method_exists('\App\Helpers\IconHelper', $icon)): ?>
            <?= \App\Helpers\IconHelper::$icon(24, 'currentColor') ?>
            <?php
        else: ?>
            <?= $label ?? 'Button' ?>
            <?php
        endif; ?>
    </button>
<?php } else { ?>
    <a href="<?= $url; ?>" <?= isset($id) ? 'id="' . htmlspecialchars($id) . '"' : '' ?>     <?= isset($ariaLabel) ? 'aria-label="' . htmlspecialchars($ariaLabel) . '"' : '' ?> class="
    <?= implode(' ', array_filter($classes)) ?>" <?= $isDisabled ? 'disabled' : '' ?>>
        <?php if ($icon && method_exists('\App\Helpers\IconHelper', $icon)): ?>
            <?= \App\Helpers\IconHelper::$icon(24, 'currentColor') ?>
            <?php
        else: ?>
            <?= $label ?? 'Button' ?>
            <?php
        endif; ?>
    </a>
<?php } ?>