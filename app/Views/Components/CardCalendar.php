<?php
use App\Helpers\Component;
use App\Helpers\I18n;

// --- Default Props ---
$date = $date ?? '14 MARS';
$city = $city ?? 'ISTRES';
$variant = $variant ?? 'gray';

// --- Hover Props ---
$idEvent = $idEvent ?? '1';
$event = $event ?? '';
$checkoutUrl = $checkoutUrl ?? 'https://checkout.lebillet.eu/';
$ticketUrl = $ticketUrl ?? '';
$fullTime = $fullTime ?? '14 Mars - Samedi 21h00';
$locationDetails = $locationDetails ?? "L'Usine, Istres, FRA";
$doorsOpen = $doorsOpen ?? 'Ouverture des portes: 19h';
$statusText = $statusText ?? 'Dernières places';
$isFree = $isFree ?? false;

$hasTicket = !empty($ticketUrl);
$fillColor = ($variant === 'gray') ? 'var(--color-neutral-500)' : 'var(--color-primary-300)';

$buttonLabel = $hasTicket ? 'RÉSERVER MON BILLET' : 'BIENTÔT DISPONIBLE';
$buttonState = $hasTicket ? 'default' : 'disabled';

$buttonComponent = $isFree ? '' : Component::render('Button', [
    'idEvent' => $idEvent,
    'event' => $event,
    'variant' => 'Primary', // Or any custom variant that uses Neutral-1100 bg
    'style' => 'Solid',
    'size' => 'Large',
    'label' => $buttonLabel,
    'state' => $buttonState,
    'class' => 'card-calendar-btn', // In case it needs strict overrides
    'checkoutUrl' => $checkoutUrl,
    'ticketUrl' => $ticketUrl
]);
?>

<div class="card-calendar">
    <!-- Default View -->
    <div class="card-calendar-default">
        <div class="card-calendar-image">
            <svg width="280" height="183" viewBox="0 0 280 183" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M280 0.126675V182.749L240.09 182.876L160.65 70.3044L159.892 84.9986L120.066 28.6285L119.982 0H160.439L239.88 112.403L240.09 0.126675H280Z"
                    fill="<?= $fillColor ?>" />
                <path
                    d="M160.102 154.416L160.186 182.834H120.108L40.1624 69.6288L39.6572 181.778L0.0421316 95.0059L0 0.168899L40.5413 0.126675L119.519 111.98L120.361 95.7238L160.102 154.416Z"
                    fill="<?= $fillColor ?>" />
            </svg>
        </div>
        <div class="card-calendar-content-default">
            <p class="card-calendar-date-default">
                <?= htmlspecialchars($date) ?>
            </p>
            <p class="card-calendar-city-default">
                <?= htmlspecialchars($city) ?>
            </p>
        </div>
    </div>

    <!-- Hover View (Hidden by default, shown on parent hover via CSS) -->
    <div class="card-calendar-hover">
        <div class="card-calendar-details">
            <p>
                <?= htmlspecialchars($fullTime) ?>
            </p>
            <p>
                <?= htmlspecialchars($locationDetails) ?>
            </p>
            <p>
                <?= htmlspecialchars($doorsOpen) ?>
            </p>
        </div>

        <?php if ($isFree): ?>
        <div class="card-calendar-button-wrap">
            <span class="card-calendar-free-badge"><?= htmlspecialchars(I18n::t('dates.free')) ?></span>
        </div>
        <?php else: ?>
        <div class="card-calendar-button-wrap">
            <?= $buttonComponent ?>
        </div>

        <?php if ($hasTicket): ?>
        <p class="card-calendar-status">
            <?= htmlspecialchars($statusText) ?>
        </p>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>