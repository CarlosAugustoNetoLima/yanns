<?php

use App\Helpers\Component;

$dates = [
    [
        'state' => 'Default',
        'dayName' => 'mar.',
        'dayNumber' => '14',
        'locationText' => 'Istres, France',
        'timeAndVenue' => "sam., 19:00 · L'Usine"
    ]
];

$dates = [];
foreach ($events as $ev) {
    $date = new DateTime($ev->date_start);
    $dateNow = new DateTime();

    $diaSemana = $date->format('D'); // dia da semana abreviado
    $mes  = $date->format('M'); // mês abreviado
    $dia  = $date->format('d'); // dia
    $ano  = $date->format('Y'); // ano
    $hora = $date->format('H:m'); // hora

    array_push($dates, [
        'idEvent' => $ev->id,
        'event' => $ev->name,
        'checkoutUrl' => $checkoutUrl,
        'state' => 'Default',
        'dayName' => $mes . '.',
        'dayNumber' => $dia,
        'locationText' => $ev->city->name . ', ' . $ev->country->name,
        'timeAndVenue' => $diaSemana . ". " . $hora . " · " . $ev->place->name
    ]);
}
?>

<section class="dates-section" data-name="Dates section">
    <div class="dates-bg-wrapper" data-name="yanns dates">
        <!-- SVG will be loaded here once exported from Figma -->
        <img src="assets/img/yanns-dates-bg.svg" alt="" class="dates-bg-image">
    </div>

    <h2 class="dates-title">
        <span class="dates-title-text">DATES </span>
        <span class="dates-title-year"><?=  $dateNow->format('Y'); ?></span>
    </h2>

    <div class="dates-container" data-name="Dates">
        <div class="dates-grid" data-name="grid dates">
            <?php foreach ($dates as $date): ?>
                <?= Component::render('CardDate', $date) ?>
            <?php
            endforeach; ?>
        </div>
    </div>

    <div class="dates-action">
        <?= Component::render('Button', [
            'url' => '/dates',
            'label' => 'VOIR PLUS',
            'variant' => 'primary',
            'style' => 'solid',
            'size' => 'extra-large'
        ]) ?>
    </div>
</section>