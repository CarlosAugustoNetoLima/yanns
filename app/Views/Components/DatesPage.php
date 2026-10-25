<?php
use App\Helpers\Component;
use App\Helpers\I18n;

// Groups events by year, keeping the original ordering within each year
$datesByYear = [];
$vari = false;
foreach ($events as $ev) {
    $vari = !$vari;
    $dateStart = new DateTime($ev->date_start);
    $mes  = $dateStart->format('M'); // mês abreviado
    $dia  = $dateStart->format('d'); // dia
    $ano  = $dateStart->format('Y'); // ano
    $isFree = !empty($ev->isFree);
    $timeTbd = !empty($ev->timeTbd);

    $doorsOpenText = '';
    if (!$timeTbd && !empty($ev->date_opening)) {
        $dateOpen = new DateTime($ev->date_opening);
        $doorsOpenText = I18n::t('dates.doors_open', ['time' => $dateOpen->format('H')]);
    } elseif ($timeTbd) {
        $doorsOpenText = I18n::t('dates.time_tbd');
    }

    if (!isset($datesByYear[$ano])) {
        $datesByYear[$ano] = [];
    }

    array_push($datesByYear[$ano], [
        'date' => $dia . ' ' . $mes,
        'idEvent' => $ev->id,
        'event' => $ev->name ?? '',
        'checkoutUrl' => $checkoutUrl,
        'ticketUrl' => $ev->ticketUrl ?? $checkoutUrl,
        'city' => $ev->city->name,
        'variant' => !$vari ? 'pink' : 'gray',
        'fullTime' => $timeTbd ? $dateStart->format('d M - D') : $dateStart->format('d M - D H:i'),
        'locationDetails' => $ev->place->name,
        'doorsOpen' => $doorsOpenText,
        'isFree' => $isFree
    ]);
}

ksort($datesByYear);
$isFirstYearGroup = true;
?>

<section class="dates-page-light" data-name="Dates page">
    <div class="container dates-page-container">

        <?php foreach ($datesByYear as $ano => $dates): ?>
        <div class="dates-page-year-group" data-name="Dates <?= htmlspecialchars($ano) ?>">

            <header class="dates-page-header">
                <h2 class="dates-page-title">
                    <span class="dates-page-title-text"><?= htmlspecialchars(I18n::t('dates.tour_title')) ?></span>
                    <span class="dates-page-title-year"><?= htmlspecialchars($ano) ?></span>
                </h2>
            </header>

            <div class="dates-page-content">
                <!-- Rotated Sidebar Text (only on the first year group) -->
                <div class="dates-page-sidebar">
                    <?php if ($isFirstYearGroup): ?>
                    <span class="dates-sidebar-text"><?= I18n::t('dates.tickets_official') ?></span>
                    <?php endif; ?>
                </div>

                <!-- Grid using specifically tailored CardCalendars -->
                <div class="dates-page-grid" data-name="grid dates">
                    <?php foreach ($dates as $date): ?>
                    <?= Component::render('CardCalendar', $date)?>
                    <?php
endforeach; ?>
                </div>
            </div>

        </div>
        <?php
$isFirstYearGroup = false;
endforeach; ?>

    </div>
</section>