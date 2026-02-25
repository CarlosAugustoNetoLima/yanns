<?php
// Props: state ('Default', 'Hover'), dayName ('mar.'), dayNumber ('14'), locationText ('Istres, France'), timeAndVenue ("sam., 19:00 · L'Usine")
// Using 'Default' as fallback state
$state = $state ?? 'Default';
$dayName = $dayName ?? 'mar.';
$dayNumber = $dayNumber ?? '14';
$locationText = $locationText ?? 'Istres, France';
$timeAndVenue = $timeAndVenue ?? "sam., 19:00 · L'Usine";

$isDefault = $state === 'Default';
$isHover = $state === 'Hover';

$cardClass = 'card-date ' . strtolower($state);
// Adding a fixed class reference for easier targeted styling in context of styleguide previews
if (isset($class)) {
    $cardClass .= ' ' . $class;
}
?>
<div class="<?= htmlspecialchars($cardClass)?>">
    <!-- Hover Text Overlay -->
    <div class="card-date-hover-text">BILLETTERIE</div>

    <div class="card-date-left">
        <p class="card-date-day-name">
            <?= htmlspecialchars($dayName)?>
        </p>
        <p class="card-date-day-number">
            <?= htmlspecialchars($dayNumber)?>
        </p>
    </div>
    <div class="card-date-right">
        <p class="card-date-location">
            <?= htmlspecialchars($locationText)?>
        </p>
        <p class="card-date-time-venue">
            <?= htmlspecialchars($timeAndVenue)?>
        </p>
    </div>
</div>