<?php
use App\Helpers\Component;

$dates = [
    [
        'state' => 'Default',
        'dayName' => 'mar.',
        'dayNumber' => '14',
        'locationText' => 'Istres, France',
        'timeAndVenue' => "sam., 19:00 · L'Usine"
    ],
    [
        'state' => 'Default',
        'dayName' => 'mar.',
        'dayNumber' => '28',
        'locationText' => 'Brest, France',
        'timeAndVenue' => "sam., 00:00 · Cabaret Vauban"
    ],
    [
        'state' => 'Default',
        'dayName' => 'mar.',
        'dayNumber' => '28',
        'locationText' => 'Brest, France',
        'timeAndVenue' => "sam., 19:00 · Cabaret Vauban"
    ],
    [
        'state' => 'Default',
        'dayName' => 'mar.',
        'dayNumber' => '29',
        'locationText' => 'Rennes, France',
        'timeAndVenue' => "dim., 18:00 · Le Liberté"
    ],
    [
        'state' => 'Default',
        'dayName' => 'avr.',
        'dayNumber' => '8',
        'locationText' => 'Nantes, France',
        'timeAndVenue' => "mer., 19:00 · Warehouse"
    ],
    [
        'state' => 'Default',
        'dayName' => 'avr.',
        'dayNumber' => '19',
        'locationText' => 'Paris, France',
        'timeAndVenue' => "dim., 16:00 · Olympia"
    ]
];
?>

<section class="dates-section" data-name="Dates section">
    <div class="dates-bg-wrapper" data-name="yanns dates">
        <!-- SVG will be loaded here once exported from Figma -->
        <img src="assets/img/yanns-dates-bg.svg" alt="" class="dates-bg-image">
    </div>

    <h2 class="dates-title">
        <span class="dates-title-text">DATES </span>
        <span class="dates-title-year">2026</span>
    </h2>

    <div class="dates-container" data-name="Dates">
        <div class="dates-grid" data-name="grid dates">
            <?php foreach ($dates as $date): ?>
            <?= Component::render('CardDate', $date)?>
            <?php
endforeach; ?>
        </div>
    </div>

    <div class="dates-action">
        <?= Component::render('Button', [
    'label' => 'VOIR PLUS',
    'variant' => 'primary',
    'style' => 'solid',
    'size' => 'extra-large'
])?>
    </div>
</section>