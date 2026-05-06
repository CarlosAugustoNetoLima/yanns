<?php
use App\Helpers\Component;

$tabs = [
    'ALL',
];

?>
<section class="collections-section">
    <div class="collections-container">

        <!-- Tabs -->
        <div class="collections-tabs" id="collections-tabs">
            <?php foreach ($tabs as $index => $tab): ?>
            <?= Component::render('Tab', [
        'label' => $tab,
        'active' => $index === 0,
        'class' => 'collection-tab-item'
    ])?>
            <?php
endforeach; ?>
        </div>

        <!-- Grid (Will be populated by JS from API) -->
        <div class="collections-grid" id="collections-grid">
            <div class="collections-loading">Carregando produtos...</div>
        </div>

    </div>
</section>