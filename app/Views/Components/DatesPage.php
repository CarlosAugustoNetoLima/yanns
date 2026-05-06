<?php
use App\Helpers\Component;

// Updated to use the exactly mapped `CardCalendar` props: date, city, variant
$dates = [
    [
        'date' => '14 MARS',
        'city' => 'ISTRES',
        'variant' => 'gray'
    ],
    [
        'date' => '14 MARS',
        'city' => 'ISTRES',
        'variant' => 'pink'
    ],
    [
        'date' => '14 MARS',
        'city' => 'ISTRES',
        'variant' => 'gray'
    ],
    [
        'date' => '14 MARS',
        'city' => 'ISTRES',
        'variant' => 'pink'
    ],
    [
        'date' => '14 MARS',
        'city' => 'ISTRES',
        'variant' => 'gray'
    ],
    [
        'date' => '14 MARS',
        'city' => 'ISTRES',
        'variant' => 'pink'
    ],
    [
        'date' => '14 MARS',
        'city' => 'ISTRES',
        'variant' => 'gray'
    ],
    [
        'date' => '14 MARS',
        'city' => 'ISTRES',
        'variant' => 'pink'
    ]
];

$dates = [];
$vari = false;
foreach ($events as $ev) {
    $vari = !$vari;
    $dateStart = new DateTime($ev->date_start);
    $mes  = $dateStart->format('M'); // mês abreviado
    $dia  = $dateStart->format('d'); // dia

    $doorsOpenText = '';
    if (!empty($ev->date_opening)) {
        $dateOpen = new DateTime($ev->date_opening);
        $doorsOpenText = 'Ouverture des portes: ' . $dateOpen->format('H') . 'h';
    }

    array_push($dates, [
        'date' => $dia . ' ' . $mes,
        'idEvent' => $ev->id,
        'event' => $ev->name ?? '',
        'checkoutUrl' => $checkoutUrl,
        'ticketUrl' => $ev->ticketUrl ?? $checkoutUrl,
        'city' => $ev->city->name,
        'variant' => !$vari ? 'pink' : 'gray',
        'fullTime' => $dateStart->format('d M - D H:i'),
        'locationDetails' => $ev->place->name,
        'doorsOpen' => $doorsOpenText
    ]);
}

$dateNow = new DateTime();
?>

<section class="dates-page-light" data-name="Dates page">
    <div class="container dates-page-container">

        <header class="dates-page-header">
            <h2 class="dates-page-title">
                <span class="dates-page-title-text">DATES DE TOURNÉE</span>
                <span class="dates-page-title-year"><?=  $dateNow->format('Y'); ?></span>
            </h2>
        </header>

        <div class="dates-page-content">
            <!-- Rotated Sidebar Text -->
            <div class="dates-page-sidebar">
                <span class="dates-sidebar-text">BILLETTERIE<br>OFFICIELLE</span>
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
</section>