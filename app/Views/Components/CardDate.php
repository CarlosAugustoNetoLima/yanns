<?php
use App\Helpers\I18n;

// Props: state ('Default', 'Hover'), dayName ('mar.'), dayNumber ('14'), locationText ('Istres, France'), timeAndVenue ("sam., 19:00 · L'Usine")
// Using 'Default' as fallback state
$state = $state ?? 'Default';
$dayName = $dayName ?? 'mar.';
$dayNumber = $dayNumber ?? '14';
$locationText = $locationText ?? 'Istres, France';
$timeAndVenue = $timeAndVenue ?? "sam., 19:00 · L'Usine";
$idEvent = $idEvent ?? '1';
$event = $event ?? '';
$checkoutUrl = $checkoutUrl ?? 'https://checkout.lebillet.eu/';
$ticketUrl = $ticketUrl ?? '';
$isFree = $isFree ?? false;

$hasTicket = !empty($ticketUrl);
$isDefault = $state === 'Default';
$isHover = $state === 'Hover';

$cardClass = 'card-date ' . strtolower($state);
if (!$hasTicket) {
    $cardClass .= ' no-ticket';
}
if ($isFree) {
    $cardClass .= ' free';
}
// Adding a fixed class reference for easier targeted styling in context of styleguide previews
if (isset($class)) {
    $cardClass .= ' ' . $class;
}
?>
<div class="<?= htmlspecialchars($cardClass) ?>"
    <?php if ($hasTicket && !$isFree): ?>onclick="openCustomModalNew('<?php echo htmlspecialchars($event); ?>', '<?php echo $ticketUrl; ?>')"<?php endif; ?>>

    <!-- Hover Text Overlay -->
    <div class="card-date-hover-text"><?= $isFree ? htmlspecialchars(I18n::t('dates.free')) : ($hasTicket ? 'BILLETTERIE' : 'BIENTÔT') ?></div>

    <div class="card-date-left">
        <p class="card-date-day-name">
            <?= htmlspecialchars($dayName) ?>
        </p>
        <p class="card-date-day-number">
            <?= htmlspecialchars($dayNumber) ?>
        </p>
    </div>
    <div class="card-date-right">
        <p class="card-date-location">
            <?= htmlspecialchars($locationText) ?>
        </p>
        <p class="card-date-time-venue">
            <?= htmlspecialchars($timeAndVenue) ?>
        </p>
    </div>
</div>

<script>
    function openCustomModalNew(event, url) {
        // You can customize this function to open your modal with the event name
        // For now, it just redirects to the URL
        window.open(url, '_blank');
    }
</script>