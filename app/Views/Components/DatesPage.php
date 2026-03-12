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
?>

<section class="dates-page-light" data-name="Dates page">
    <div class="container dates-page-container">

        <header class="dates-page-header">
            <h2 class="dates-page-title">
                <span class="dates-page-title-text">DATES DE TOURNÉE</span>
                <span class="dates-page-title-year">2026</span>
            </h2>
        </header>

        <div class="dates-page-content">
            <!-- Rotated Sidebar Text -->
            <div class="dates-page-sidebar">
                <span class="dates-sidebar-text">BILLETTERIE
                    OFFICIELLE</span>
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