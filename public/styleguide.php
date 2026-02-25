<?php
require_once __DIR__ . '/../app/Helpers/Component.php';
require_once __DIR__ . '/../app/Helpers/IconHelper.php';
use App\Helpers\Component;
use App\Helpers\IconHelper;

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YANNS Styleguide</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        .styleguide-section {
            margin-bottom: var(--spacing-64);
            padding: var(--spacing-32);
            background: #fff;
            border-radius: var(--spacing-16);
            border: 1px solid var(--color-neutral-200);
        }

        .styleguide-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: var(--spacing-24);
            align-items: center;
        }

        .styleguide-grid-buttons {
            display: grid;
            grid-template-columns: repeat(6, auto);
            gap: 24px;
            align-items: end;
            justify-content: start;
            margin-bottom: 40px;
        }

        .input-active .input-container {
            border-color: var(--color-primary-500);
        }
    </style>
    <style>
        /* Styleguide specific overrides for Header Testing */
        .force-desktop .header-mobile {
            display: none !important;
        }

        .force-desktop .header-desktop,
        .force-desktop .header-navbar-desktop {
            display: flex !important;
            width: 100%;
        }

        .force-mobile .header-mobile {
            display: flex !important;
            width: 100% !important;
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            z-index: 30 !important;
            /* Highest priority */
            background-color: var(--color-neutral-100) !important;
            box-sizing: border-box !important;
        }

        .force-mobile .header-overlay {
            display: flex !important;
            position: absolute !important;
            top: 80px !important;
            /* Below header */
            left: 0 !important;
            width: 100% !important;
            height: calc(100% - 80px) !important;
            background-color: rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(4px);
            z-index: 20 !important;
            /* Below header */
            transform: translateX(100%);
            transition: transform 0.3s ease-in-out;
        }

        .force-mobile .header-overlay.active {
            transform: translateX(0);
        }

        .force-mobile .header-desktop,
        .force-mobile .header-navbar-desktop {
            display: none !important;
        }

        .force-mobile {
            min-width: 360px;
            /* Requested minimum */
            max-width: 375px;
            height: 600px;
            /* Adjusted height */
            position: relative;
            overflow: hidden;
            border: 1px solid #111;
            border-radius: 32px;
            /* Phone-like rounded corners */
            margin-bottom: 20px;
            margin-left: auto;
            margin-right: auto;
            background: #fff;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>

    <header>
        <!-- Use the normal header logic as default/global header if needed, or remove for clean slate -->
        <!-- We will put headers in display section -->
    </header>

    <main class="container" style="padding-top: var(--spacing-40); padding-bottom: var(--spacing-40);">
        <h1>Styleguide</h1>
        <p style="margin-bottom: var(--spacing-40); color: var(--color-neutral-600);">Component library verification.
        </p>

        <section class="styleguide-section" style="background: #f9f9f9;">
            <h2>Headers</h2>
            <p style="margin-bottom: var(--spacing-24); color: var(--color-neutral-600);">Responsive Header Variants.
            </p>

            <h3>Desktop Header</h3>
            <div class="force-desktop" style="background: #fff; margin-bottom: 32px;">
                <?= Component::render('Header', ['id' => 'header_desktop_demo'])?>
            </div>

            <h3>Mobile Header</h3>
            <div class="force-mobile" style="background: #fff;">
                <?= Component::render('Header', ['id' => 'header_mobile_demo'])?>
            </div>
        </section>

        <section class="styleguide-section">
            <h2>Buttons</h2>

            <h3 style="margin-top: 32px; margin-bottom: 24px;">Solid Button</h3>
            <div class="styleguide-grid-buttons"
                style="grid-template-columns: repeat(4, 1fr); gap: 40px; margin-bottom: 64px;">
                <!-- Column Headers -->
                <div style="font-weight: bold; color: var(--color-neutral-400); font-size: 12px;">Extra Large (72px)
                </div>
                <div style="font-weight: bold; color: var(--color-neutral-400); font-size: 12px;">Large (60px)</div>
                <div style="font-weight: bold; color: var(--color-neutral-400); font-size: 12px;">Medium (48px)</div>
                <div style="font-weight: bold; color: var(--color-neutral-400); font-size: 12px;">Small (28px)</div>

                <!-- Row 1: Default -->
                <div style="grid-column: 1 / -1; font-size: 12px; color: var(--color-neutral-500); margin-top: 16px;">1.
                    Default State</div>
                <div>
                    <?= Component::render('Button', ['variant' => 'Primary', 'style' => 'Solid', 'size' => 'Extra Large', 'label' => 'Button'])?>
                </div>
                <div>
                    <?= Component::render('Button', ['variant' => 'Primary', 'style' => 'Solid', 'size' => 'Large', 'label' => 'Button'])?>
                </div>
                <div>
                    <?= Component::render('Button', ['variant' => 'Primary', 'style' => 'Solid', 'size' => 'Medium', 'label' => 'Button'])?>
                </div>
                <div>
                    <?= Component::render('Button', ['variant' => 'Primary', 'style' => 'Solid', 'size' => 'Small', 'label' => 'Button'])?>
                </div>

                <!-- Row 2: Hover State -->
                <div style="grid-column: 1 / -1; font-size: 12px; color: var(--color-neutral-500); margin-top: 16px;">2.
                    Hover State</div>
                <div>
                    <?= Component::render('Button', ['variant' => 'Primary', 'style' => 'Solid', 'size' => 'Extra Large', 'state' => 'Hover', 'label' => 'Button'])?>
                </div>
                <div>
                    <?= Component::render('Button', ['variant' => 'Primary', 'style' => 'Solid', 'size' => 'Large', 'state' => 'Hover', 'label' => 'Button'])?>
                </div>
                <div>
                    <?= Component::render('Button', ['variant' => 'Primary', 'style' => 'Solid', 'size' => 'Medium', 'state' => 'Hover', 'label' => 'Button'])?>
                </div>
                <div>
                    <?= Component::render('Button', ['variant' => 'Primary', 'style' => 'Solid', 'size' => 'Small', 'state' => 'Hover', 'label' => 'Button'])?>
                </div>

                <!-- Row 3: Disabled -->
                <div style="grid-column: 1 / -1; font-size: 12px; color: var(--color-neutral-500); margin-top: 16px;">3.
                    Disabled State</div>
                <div>
                    <?= Component::render('Button', ['variant' => 'Primary', 'style' => 'Solid', 'size' => 'Extra Large', 'state' => 'Disabled', 'label' => 'Button'])?>
                </div>
                <div>
                    <?= Component::render('Button', ['variant' => 'Primary', 'style' => 'Solid', 'size' => 'Large', 'state' => 'Disabled', 'label' => 'Button'])?>
                </div>
                <div>
                    <?= Component::render('Button', ['variant' => 'Primary', 'style' => 'Solid', 'size' => 'Medium', 'state' => 'Disabled', 'label' => 'Button'])?>
                </div>
                <div>
                    <?= Component::render('Button', ['variant' => 'Primary', 'style' => 'Solid', 'size' => 'Small', 'state' => 'Disabled', 'label' => 'Button'])?>
                </div>
            </div>

            <h3 style="margin-top: 32px; margin-bottom: 24px;">Outline Button</h3>
            <div class="styleguide-grid-buttons" style="grid-template-columns: repeat(4, 1fr); gap: 40px;">
                <!-- Column Headers -->
                <div style="font-weight: bold; color: var(--color-neutral-400); font-size: 12px;">Extra Large (72px)
                </div>
                <div style="font-weight: bold; color: var(--color-neutral-400); font-size: 12px;">Large (60px)</div>
                <div style="font-weight: bold; color: var(--color-neutral-400); font-size: 12px;">Medium (48px)</div>
                <div style="font-weight: bold; color: var(--color-neutral-400); font-size: 12px;">Small (28px)</div>

                <!-- Row 1: Default -->
                <div style="grid-column: 1 / -1; font-size: 12px; color: var(--color-neutral-500); margin-top: 16px;">1.
                    Default State</div>
                <div>
                    <?= Component::render('Button', ['variant' => 'Primary', 'style' => 'Outline', 'size' => 'Extra Large', 'label' => 'Button'])?>
                </div>
                <div>
                    <?= Component::render('Button', ['variant' => 'Primary', 'style' => 'Outline', 'size' => 'Large', 'label' => 'Button'])?>
                </div>
                <div>
                    <?= Component::render('Button', ['variant' => 'Primary', 'style' => 'Outline', 'size' => 'Medium', 'label' => 'Button'])?>
                </div>
                <div>
                    <?= Component::render('Button', ['variant' => 'Primary', 'style' => 'Outline', 'size' => 'Small', 'label' => 'Button'])?>
                </div>

                <!-- Row 2: Hover State -->
                <div style="grid-column: 1 / -1; font-size: 12px; color: var(--color-neutral-500); margin-top: 16px;">2.
                    Hover State</div>
                <div>
                    <?= Component::render('Button', ['variant' => 'Primary', 'style' => 'Outline', 'size' => 'Extra Large', 'state' => 'Hover', 'label' => 'Button'])?>
                </div>
                <div>
                    <?= Component::render('Button', ['variant' => 'Primary', 'style' => 'Outline', 'size' => 'Large', 'state' => 'Hover', 'label' => 'Button'])?>
                </div>
                <div>
                    <?= Component::render('Button', ['variant' => 'Primary', 'style' => 'Outline', 'size' => 'Medium', 'state' => 'Hover', 'label' => 'Button'])?>
                </div>
                <div>
                    <?= Component::render('Button', ['variant' => 'Primary', 'style' => 'Outline', 'size' => 'Small', 'state' => 'Hover', 'label' => 'Button'])?>
                </div>

                <!-- Row 3: Disabled -->
                <div style="grid-column: 1 / -1; font-size: 12px; color: var(--color-neutral-500); margin-top: 16px;">3.
                    Disabled State</div>
                <div>
                    <?= Component::render('Button', ['variant' => 'Primary', 'style' => 'Outline', 'size' => 'Extra Large', 'state' => 'Disabled', 'label' => 'Button'])?>
                </div>
                <div>
                    <?= Component::render('Button', ['variant' => 'Primary', 'style' => 'Outline', 'size' => 'Large', 'state' => 'Disabled', 'label' => 'Button'])?>
                </div>
                <div>
                    <?= Component::render('Button', ['variant' => 'Primary', 'style' => 'Outline', 'size' => 'Medium', 'state' => 'Disabled', 'label' => 'Button'])?>
                </div>
                <div>
                    <?= Component::render('Button', ['variant' => 'Primary', 'style' => 'Outline', 'size' => 'Small', 'state' => 'Disabled', 'label' => 'Button'])?>
                </div>
            </div>
            <h3 style="margin-top: 32px; margin-bottom: 24px;">Light Button States</h3>
            <div class="styleguide-grid-buttons" style="grid-template-columns: repeat(4, 1fr); gap: 40px;">
                <!-- Column Headers -->
                <div style="font-weight: bold; color: var(--color-neutral-400); font-size: 12px;">Extra Large (72px)
                </div>
                <div style="font-weight: bold; color: var(--color-neutral-400); font-size: 12px;">Large (60px)</div>
                <div style="font-weight: bold; color: var(--color-neutral-400); font-size: 12px;">Medium (48px)</div>
                <div style="font-weight: bold; color: var(--color-neutral-400); font-size: 12px;">Small (28px)</div>

                <!-- Row 1: Default -->
                <div style="grid-column: 1 / -1; font-size: 12px; color: var(--color-neutral-500); margin-top: 16px;">1.
                    Default State</div>
                <div>
                    <?= Component::render('Button', ['variant' => 'Light', 'style' => 'Solid', 'size' => 'Extra Large', 'label' => 'Button'])?>
                </div>
                <div>
                    <?= Component::render('Button', ['variant' => 'Light', 'style' => 'Solid', 'size' => 'Large', 'label' => 'Button'])?>
                </div>
                <div>
                    <?= Component::render('Button', ['variant' => 'Light', 'style' => 'Solid', 'size' => 'Medium', 'label' => 'Button'])?>
                </div>
                <div>
                    <?= Component::render('Button', ['variant' => 'Light', 'style' => 'Solid', 'size' => 'Small', 'label' => 'Button'])?>
                </div>

                <!-- Row 2: Hover State -->
                <div style="grid-column: 1 / -1; font-size: 12px; color: var(--color-neutral-500); margin-top: 16px;">2.
                    Hover State</div>
                <div>
                    <?= Component::render('Button', ['variant' => 'Light', 'style' => 'Solid', 'size' => 'Extra Large', 'state' => 'Hover', 'label' => 'Button'])?>
                </div>
                <div>
                    <?= Component::render('Button', ['variant' => 'Light', 'style' => 'Solid', 'size' => 'Large', 'state' => 'Hover', 'label' => 'Button'])?>
                </div>
                <div>
                    <?= Component::render('Button', ['variant' => 'Light', 'style' => 'Solid', 'size' => 'Medium', 'state' => 'Hover', 'label' => 'Button'])?>
                </div>
                <div>
                    <?= Component::render('Button', ['variant' => 'Light', 'style' => 'Solid', 'size' => 'Small', 'state' => 'Hover', 'label' => 'Button'])?>
                </div>

                <!-- Row 3: Disabled -->
                <div style="grid-column: 1 / -1; font-size: 12px; color: var(--color-neutral-500); margin-top: 16px;">3.
                    Disabled State</div>
                <div>
                    <?= Component::render('Button', ['variant' => 'Light', 'size' => 'Extra Large', 'state' => 'Disabled', 'label' => 'Button'])?>
                </div>
                <div>
                    <?= Component::render('Button', ['variant' => 'Light', 'size' => 'Large', 'state' => 'Disabled', 'label' => 'Button'])?>
                </div>
                <div>
                    <?= Component::render('Button', ['variant' => 'Light', 'size' => 'Medium', 'state' => 'Disabled', 'label' => 'Button'])?>
                </div>
                <div>
                    <?= Component::render('Button', ['variant' => 'Light', 'size' => 'Small', 'state' => 'Disabled', 'label' => 'Button'])?>
                </div>
            </div>

            <h3 style="margin-top: 32px; margin-bottom: 24px;">Icon Button (Primary Solid)</h3>
            <div class="styleguide-grid-buttons" style="grid-template-columns: repeat(4, 1fr); gap: 40px;">
                <!-- Column Headers -->
                <div style="font-weight: bold; color: var(--color-neutral-400); font-size: 12px;">Extra Large (72px)
                </div>
                <div style="font-weight: bold; color: var(--color-neutral-400); font-size: 12px;">Large (60px)</div>
                <div style="font-weight: bold; color: var(--color-neutral-400); font-size: 12px;">Medium (48px)</div>
                <div style="font-weight: bold; color: var(--color-neutral-400); font-size: 12px;">Small (28px)</div>

                <!-- Row 1: Default -->
                <div style="grid-column: 1 / -1; font-size: 12px; color: var(--color-neutral-500); margin-top: 16px;">1.
                    Default State</div>
                <div>
                    <?= Component::render('Button', ['variant' => 'Primary', 'style' => 'Solid', 'size' => 'Extra Large', 'icon' => 'plus'])?>
                </div>
                <div>
                    <?= Component::render('Button', ['variant' => 'Primary', 'style' => 'Solid', 'size' => 'Large', 'icon' => 'plus'])?>
                </div>
                <div>
                    <?= Component::render('Button', ['variant' => 'Primary', 'style' => 'Solid', 'size' => 'Medium', 'icon' => 'plus'])?>
                </div>
                <div>
                    <?= Component::render('Button', ['variant' => 'Primary', 'style' => 'Solid', 'size' => 'Small', 'icon' => 'plus'])?>
                </div>

                <!-- Row 2: Hover State -->
                <div style="grid-column: 1 / -1; font-size: 12px; color: var(--color-neutral-500); margin-top: 16px;">2.
                    Hover State</div>
                <div>
                    <?= Component::render('Button', ['variant' => 'Primary', 'style' => 'Solid', 'size' => 'Extra Large', 'state' => 'Hover', 'icon' => 'plus'])?>
                </div>
                <div>
                    <?= Component::render('Button', ['variant' => 'Primary', 'style' => 'Solid', 'size' => 'Large', 'state' => 'Hover', 'icon' => 'plus'])?>
                </div>
                <div>
                    <?= Component::render('Button', ['variant' => 'Primary', 'style' => 'Solid', 'size' => 'Medium', 'state' => 'Hover', 'icon' => 'plus'])?>
                </div>
                <div>
                    <?= Component::render('Button', ['variant' => 'Primary', 'style' => 'Solid', 'size' => 'Small', 'state' => 'Hover', 'icon' => 'plus'])?>
                </div>

                <!-- Row 3: Disabled -->
                <div style="grid-column: 1 / -1; font-size: 12px; color: var(--color-neutral-500); margin-top: 16px;">3.
                    Disabled State</div>
                <div>
                    <?= Component::render('Button', ['variant' => 'Primary', 'style' => 'Solid', 'size' => 'Extra Large', 'state' => 'Disabled', 'icon' => 'plus'])?>
                </div>
                <div>
                    <?= Component::render('Button', ['variant' => 'Primary', 'style' => 'Solid', 'size' => 'Large', 'state' => 'Disabled', 'icon' => 'plus'])?>
                </div>
                <div>
                    <?= Component::render('Button', ['variant' => 'Primary', 'style' => 'Solid', 'size' => 'Medium', 'state' => 'Disabled', 'icon' => 'plus'])?>
                </div>
                <div>
                    <?= Component::render('Button', ['variant' => 'Primary', 'style' => 'Solid', 'size' => 'Small', 'state' => 'Disabled', 'icon' => 'plus'])?>
                </div>
            </div>
        </section>

        <section class="styleguide-section" style="background: var(--color-neutral-100);">
            <h2>Inputs</h2>
            <p style="margin-bottom: var(--spacing-24); color: var(--color-neutral-600);">Testing all variations of the
                Input component.</p>

            <h3 style="margin-top: 32px; margin-bottom: 24px;">Light Mode (Type: Input)</h3>
            <div class="styleguide-grid-buttons" style="grid-template-columns: repeat(3, 1fr); gap: 40px;">
                <!-- Column Headers -->
                <div style="font-weight: bold; color: var(--color-neutral-400); font-size: 12px;">Large</div>
                <div style="font-weight: bold; color: var(--color-neutral-400); font-size: 12px;">Medium</div>
                <div style="font-weight: bold; color: var(--color-neutral-400); font-size: 12px;">Small</div>

                <!-- Row 1: Default -->
                <div style="grid-column: 1 / -1; font-size: 12px; color: var(--color-neutral-500); margin-top: 16px;">1.
                    Default State (All options enabled)</div>
                <div>
                    <?= Component::render('Input', ['size' => 'Large', 'labelText' => 'Label', 'optional' => true, 'required' => true, 'info' => true, 'iconLeft' => true, 'iconRight' => true])?>
                </div>
                <div>
                    <?= Component::render('Input', ['size' => 'Medium', 'labelText' => 'Label', 'optional' => true, 'required' => true, 'info' => true, 'iconLeft' => true, 'iconRight' => true])?>
                </div>
                <div>
                    <?= Component::render('Input', ['size' => 'Small', 'labelText' => 'Label', 'optional' => true, 'required' => true, 'info' => true, 'iconLeft' => true, 'iconRight' => true])?>
                </div>

                <!-- Row 2: Active / Focus -->
                <div style="grid-column: 1 / -1; font-size: 12px; color: var(--color-neutral-500); margin-top: 16px;">2.
                    Active State</div>
                <div>
                    <?= Component::render('Input', ['size' => 'Large', 'labelText' => 'Label', 'status' => 'Active', 'iconLeft' => true, 'iconRight' => true])?>
                </div>
                <div>
                    <?= Component::render('Input', ['size' => 'Medium', 'labelText' => 'Label', 'status' => 'Active', 'iconLeft' => true, 'iconRight' => true])?>
                </div>
                <div>
                    <?= Component::render('Input', ['size' => 'Small', 'labelText' => 'Label', 'status' => 'Active', 'iconLeft' => true, 'iconRight' => true])?>
                </div>

                <!-- Row 3: Filled -->
                <div style="grid-column: 1 / -1; font-size: 12px; color: var(--color-neutral-500); margin-top: 16px;">3.
                    Filled State</div>
                <div>
                    <?= Component::render('Input', ['size' => 'Large', 'labelText' => 'Label', 'status' => 'Filled', 'value' => 'Filled text', 'iconLeft' => true, 'iconRight' => true])?>
                </div>
                <div>
                    <?= Component::render('Input', ['size' => 'Medium', 'labelText' => 'Label', 'status' => 'Filled', 'value' => 'Filled text', 'iconLeft' => true, 'iconRight' => true])?>
                </div>
                <div>
                    <?= Component::render('Input', ['size' => 'Small', 'labelText' => 'Label', 'status' => 'Filled', 'value' => 'Filled text', 'iconLeft' => true, 'iconRight' => true])?>
                </div>

                <!-- Row 4: Error -->
                <div style="grid-column: 1 / -1; font-size: 12px; color: var(--color-neutral-500); margin-top: 16px;">4.
                    Error State</div>
                <div>
                    <?= Component::render('Input', ['size' => 'Large', 'labelText' => 'Label', 'status' => 'Error', 'error' => 'Error message here', 'iconLeft' => true, 'iconRight' => true])?>
                </div>
                <div>
                    <?= Component::render('Input', ['size' => 'Medium', 'labelText' => 'Label', 'status' => 'Error', 'error' => 'Error message here', 'iconLeft' => true, 'iconRight' => true])?>
                </div>
                <div>
                    <?= Component::render('Input', ['size' => 'Small', 'labelText' => 'Label', 'status' => 'Error', 'error' => 'Error message here', 'iconLeft' => true, 'iconRight' => true])?>
                </div>

                <!-- Row 5: Disabled -->
                <div style="grid-column: 1 / -1; font-size: 12px; color: var(--color-neutral-500); margin-top: 16px;">5.
                    Disabled State</div>
                <div>
                    <?= Component::render('Input', ['size' => 'Large', 'labelText' => 'Label', 'status' => 'Disabled', 'disabled' => true, 'iconLeft' => true, 'iconRight' => true])?>
                </div>
                <div>
                    <?= Component::render('Input', ['size' => 'Medium', 'labelText' => 'Label', 'status' => 'Disabled', 'disabled' => true, 'iconLeft' => true, 'iconRight' => true])?>
                </div>
                <div>
                    <?= Component::render('Input', ['size' => 'Small', 'labelText' => 'Label', 'status' => 'Disabled', 'disabled' => true, 'iconLeft' => true, 'iconRight' => true])?>
                </div>
            </div>

            <h3 style="margin-top: 32px; margin-bottom: 24px;">Light Mode (Type: Select-Button)</h3>
            <div class="styleguide-grid-buttons"
                style="grid-template-columns: repeat(3, 1fr); gap: 40px; padding: 32px; border-radius: 8px;">
                <!-- Column Headers -->
                <div style="font-weight: bold; color: var(--color-neutral-400); font-size: 12px;">Large</div>
                <div style="font-weight: bold; color: var(--color-neutral-400); font-size: 12px;">Medium</div>
                <div style="font-weight: bold; color: var(--color-neutral-400); font-size: 12px;">Small</div>

                <!-- Row 1: Default -->
                <div style="grid-column: 1 / -1; font-size: 12px; color: var(--color-neutral-500); margin-top: 16px;">1.
                    Default State (Select-Button)</div>
                <div>
                    <?= Component::render('Input', ['size' => 'Large', 'type' => 'Select-Button', 'labelText' => 'Label', 'optional' => true, 'required' => true, 'info' => true, 'iconLeft' => true, 'iconRight' => true])?>
                </div>
                <div>
                    <?= Component::render('Input', ['size' => 'Medium', 'type' => 'Select-Button', 'labelText' => 'Label', 'optional' => true, 'required' => true, 'info' => true, 'iconLeft' => true, 'iconRight' => true])?>
                </div>
                <div>
                    <?= Component::render('Input', ['size' => 'Small', 'type' => 'Select-Button', 'labelText' => 'Label', 'optional' => true, 'required' => true, 'info' => true, 'iconLeft' => true, 'iconRight' => true])?>
                </div>

                <!-- Row 2: Active / Focus -->
                <div style="grid-column: 1 / -1; font-size: 12px; color: var(--color-neutral-500); margin-top: 16px;">2.
                    Active State</div>
                <div>
                    <?= Component::render('Input', ['size' => 'Large', 'type' => 'Select-Button', 'status' => 'Active', 'labelText' => 'Label', 'iconLeft' => true, 'iconRight' => true])?>
                </div>
                <div>
                    <?= Component::render('Input', ['size' => 'Medium', 'type' => 'Select-Button', 'status' => 'Active', 'labelText' => 'Label', 'iconLeft' => true, 'iconRight' => true])?>
                </div>
                <div>
                    <?= Component::render('Input', ['size' => 'Small', 'type' => 'Select-Button', 'status' => 'Active', 'labelText' => 'Label', 'iconLeft' => true, 'iconRight' => true])?>
                </div>

                <!-- Row 3: Filled -->
                <div style="grid-column: 1 / -1; font-size: 12px; color: var(--color-neutral-500); margin-top: 16px;">3.
                    Filled State</div>
                <div>
                    <?= Component::render('Input', ['size' => 'Large', 'type' => 'Select-Button', 'status' => 'Filled', 'labelText' => 'Label', 'value' => 'Filled text', 'iconLeft' => true, 'iconRight' => true])?>
                </div>
                <div>
                    <?= Component::render('Input', ['size' => 'Medium', 'type' => 'Select-Button', 'status' => 'Filled', 'labelText' => 'Label', 'value' => 'Filled text', 'iconLeft' => true, 'iconRight' => true])?>
                </div>
                <div>
                    <?= Component::render('Input', ['size' => 'Small', 'type' => 'Select-Button', 'status' => 'Filled', 'labelText' => 'Label', 'value' => 'Filled text', 'iconLeft' => true, 'iconRight' => true])?>
                </div>

                <!-- Row 4: Error -->
                <div style="grid-column: 1 / -1; font-size: 12px; color: var(--color-neutral-500); margin-top: 16px;">4.
                    Error State</div>
                <div>
                    <?= Component::render('Input', ['size' => 'Large', 'type' => 'Select-Button', 'status' => 'Error', 'labelText' => 'Label', 'error' => 'Error message here', 'iconLeft' => true, 'iconRight' => true])?>
                </div>
                <div>
                    <?= Component::render('Input', ['size' => 'Medium', 'type' => 'Select-Button', 'status' => 'Error', 'labelText' => 'Label', 'error' => 'Error message here', 'iconLeft' => true, 'iconRight' => true])?>
                </div>
                <div>
                    <?= Component::render('Input', ['size' => 'Small', 'type' => 'Select-Button', 'status' => 'Error', 'labelText' => 'Label', 'error' => 'Error message here', 'iconLeft' => true, 'iconRight' => true])?>
                </div>

                <!-- Row 5: Disabled -->
                <div style="grid-column: 1 / -1; font-size: 12px; color: var(--color-neutral-500); margin-top: 16px;">5.
                    Disabled State</div>
                <div>
                    <?= Component::render('Input', ['size' => 'Large', 'type' => 'Select-Button', 'status' => 'Disabled', 'labelText' => 'Label', 'disabled' => true, 'iconLeft' => true, 'iconRight' => true])?>
                </div>
                <div>
                    <?= Component::render('Input', ['size' => 'Medium', 'type' => 'Select-Button', 'status' => 'Disabled', 'labelText' => 'Label', 'disabled' => true, 'iconLeft' => true, 'iconRight' => true])?>
                </div>
                <div>
                    <?= Component::render('Input', ['size' => 'Small', 'type' => 'Select-Button', 'status' => 'Disabled', 'labelText' => 'Label', 'disabled' => true, 'iconLeft' => true, 'iconRight' => true])?>
                </div>
            </div>
        </section>

        <section class="styleguide-section">
            <h2>Tabs</h2>
            <p style="margin-bottom: var(--spacing-24); color: var(--color-neutral-600);">New Tab component with exactly
                3 states: Default, Hover, and Active.</p>
            <div class="styleguide-grid">
                <div>
                    <p style="font-size: 12px; margin-bottom: 8px; color: var(--color-neutral-500);">1. Default (White)
                    </p>
                    <?= Component::render('Tab', ['label' => 'HOME'])?>
                </div>
                <div>
                    <p style="font-size: 12px; margin-bottom: 8px; color: var(--color-neutral-500);">2. Hover (Black)
                    </p>
                    <?= Component::render('Tab', ['label' => 'HOME', 'class' => 'tab-hover'])?>
                </div>
                <div>
                    <p style="font-size: 12px; margin-bottom: 8px; color: var(--color-neutral-500);">3. Active (Pink -
                        Non-clickable)</p>
                    <?= Component::render('Tab', ['label' => 'HOME', 'active' => true])?>
                </div>
            </div>
        </section>

        <section class="styleguide-section">
            <h2>Menu Items</h2>
            <div class="styleguide-grid">
                <div>
                    <p style="font-size: 12px; margin-bottom: 8px; color: var(--color-neutral-500);">1. Default</p>
                    <?= Component::render('MenuItem', ['label' => 'HOME'])?>
                </div>
                <div>
                    <p style="font-size: 12px; margin-bottom: 8px; color: var(--color-neutral-500);">2. Hover</p>
                    <?= Component::render('MenuItem', ['label' => 'HOME', 'class' => 'menu-item-hover'])?>
                </div>
                <div>
                    <p style="font-size: 12px; margin-bottom: 8px; color: var(--color-neutral-500);">3. Active</p>
                    <?= Component::render('MenuItem', ['label' => 'HOME', 'active' => true])?>
                </div>
            </div>
        </section>

        <section class="styleguide-section" style="background: var(--color-neutral-100);">
            <h2>Dropdown Components</h2>
            <p style="margin-bottom: var(--spacing-24); color: var(--color-neutral-600);">Dropdown container and
                distinct states of internal menu items.</p>

            <div style="display: flex; gap: 40px; align-items: flex-start;">
                <div>
                    <h3 style="margin-bottom: 16px; font-size: 14px; color: var(--color-neutral-500);">1. Menu Item
                        Component</h3>
                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        <div>
                            <p style="font-size: 12px; margin-bottom: 8px; color: var(--color-neutral-500);">Default
                                State</p>
                            <?= Component::render('DropdownMenuItem', ['label' => 'Menu'])?>
                        </div>
                        <div>
                            <p style="font-size: 12px; margin-bottom: 8px; color: var(--color-neutral-500);">Hover State
                            </p>
                            <?= Component::render('DropdownMenuItem', ['label' => 'Menu', 'state' => 'Hover'])?>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 style="margin-bottom: 16px; font-size: 14px; color: var(--color-neutral-500);">2. Dropdown
                        Component Assembly</h3>
                    <p style="font-size: 12px; margin-bottom: 8px; color: var(--color-neutral-500);">Complete list with
                        hover effect simulated on the second item</p>
                    <?= Component::render('Dropdown', [
    'items' => [
        ['label' => 'Menu 1', 'state' => 'Default', 'href' => '#'],
        ['label' => 'Menu 2 (Hover)', 'state' => 'Hover', 'href' => '#'],
        ['label' => 'Menu 3', 'state' => 'Default', 'href' => '#'],
        ['label' => 'Menu 4', 'state' => 'Default', 'href' => '#']
    ]
])?>
                </div>
            </div>
        </section>

        <section class="styleguide-section">
            <h2>Navigation Menu</h2>
            <p style="margin-bottom: var(--spacing-24); color: var(--color-neutral-600);">Full navigation menu with
                border collapsing logic.</p>
            <?= Component::render('Menu')?>
        </section>

        <section class="styleguide-section">
            <h2>Mobile Menu</h2>
            <p style="margin-bottom: var(--spacing-24); color: var(--color-neutral-600);">Vertical menu for mobile
                overlay.</p>
            <div style="border: 1px solid var(--color-neutral-200); max-width: 360px;">
                <?= Component::render('MobileMenu')?>
            </div>
        </section>

        <section class="styleguide-section">
            <h2>Store Cards</h2>
            <p style="margin-bottom: var(--spacing-24); color: var(--color-neutral-600);">Responsive product cards.
                Resize window to &lt; 768px to see mobile version.</p>
            <div class="styleguide-grid">
                <?= Component::render('StoreCard', [
    'title' => 'CELLE/CELUI - CASQUETTE',
    'price' => '24,90 €',
    'image' => 'https://placehold.co/200x200?text=Cap' // Ensure this exists or use a placehold.co link
])?>
                <?= Component::render('StoreCard', [
    'title' => 'ALBUM DÉCEMBRE',
    'price' => '10,99 €',
    'image' => 'https://placehold.co/300x300'
])?>
            </div>
        </section>

        <section class="styleguide-section">
            <h2>Card Date</h2>
            <p style="margin-bottom: var(--spacing-24); color: var(--color-neutral-600);">Tour date and location card
                (Default and Hover states).</p>
            <div class="styleguide-grid" style="grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));">
                <div>
                    <p style="font-size: 12px; margin-bottom: 8px; color: var(--color-neutral-500);">1. Default State
                    </p>
                    <?= Component::render('CardDate')?>
                </div>
                <div>
                    <p style="font-size: 12px; margin-bottom: 8px; color: var(--color-neutral-500);">2. Hover State</p>
                    <?= Component::render('CardDate', ['state' => 'Hover'])?>
                </div>
            </div>
        </section>

        <section class="styleguide-section">
            <h2>Youtube Cards</h2>
            <p style="margin-bottom: var(--spacing-24); color: var(--color-neutral-600);">Component for displaying
                YouTube videos.</p>
            <div class="styleguide-grid">
                <div>
                    <?= Component::render('CardYoutube')?>
                </div>
            </div>
        </section>

        <section class="styleguide-section">
            <h2>Icons</h2>
            <p style="margin-bottom: var(--spacing-24); color: var(--color-neutral-600);">Displaying a subset of icons
                with different sizes and colors.</p>

            <div class="styleguide-grid">
                <div style="text-align: center;">
                    <?= IconHelper::home(32, 'var(--color-primary-500)')?>
                    <p style="font-size: 12px; margin-top: 8px;">Home (32px, Primary)</p>
                </div>
                <div style="text-align: center;">
                    <?= IconHelper::tikTok(32, '#000000')?>
                    <p style="font-size: 12px; margin-top: 8px;">TikTok (32px, Black)</p>
                </div>
                <div style="text-align: center;">
                    <?= IconHelper::abTesting(32, 'var(--color-neutral-600)')?>
                    <p style="font-size: 12px; margin-top: 8px;">AB Testing (32px, Neutral)</p>
                </div>
                <div style="text-align: center;">
                    <?= IconHelper::account(32, 'var(--color-info-500-main)')?>
                    <p style="font-size: 12px; margin-top: 8px;">Account (32px, Info)</p>
                </div>
            </div>

            <h3>Player & Services (Direct Helper Test)</h3>
            <div class="styleguide-grid" style="background: #000; padding: 20px; border-radius: 8px;">
                <div style="text-align: center;">
                    <?= IconHelper::controlPlay(48, 'var(--color-neutral-0)')?>
                    <p style="font-size: 12px; margin-top: 8px; color: var(--color-neutral-0);">Play (Custom Color)</p>
                </div>
                <div style="text-align: center;">
                    <?= IconHelper::controlPause(48, 'var(--color-neutral-0)')?>
                    <p style="font-size: 12px; margin-top: 8px; color: var(--color-neutral-0);">Pause (White)</p>
                </div>
                <div style="text-align: center;">
                    <?= IconHelper::brandSpotify(48)?>
                    <p style="font-size: 12px; margin-top: 8px; color: var(--color-neutral-0);">Spotify (Default)</p>
                </div>
                <div style="text-align: center;">
                    <?= IconHelper::brandApple(48)?>
                    <p style="font-size: 12px; margin-top: 8px; color: var(--color-neutral-0);">Apple (Default)</p>
                </div>
            </div>
        </section>

        <section class="styleguide-section" style="background-color: #333; color: white;">
            <h2>Scroll Icon</h2>
            <p style="margin-bottom: var(--spacing-24); color: var(--color-neutral-100);">Responsive mouse scroll
                indicator (Resize to test).</p>
            <div class="styleguide-grid" style="justify-content: center; padding: 40px;">
                <?= Component::render('ScrollIcon')?>
            </div>
        </section>

        <section class="styleguide-section" style="background-color: #000; color: white;">
            <h2>Component Sets (Glassmorphism)</h2>
            <p style="margin-bottom: var(--spacing-24); color: var(--color-neutral-100);">Video, Sound, and Music
                Service controls.</p>

            <h3>Video Control</h3>
            <div class="styleguide-grid">
                <?= Component::render('VideoControl', ['state' => 'play'])?>
                <?= Component::render('VideoControl', ['state' => 'pause'])?>
            </div>

            <h3>Sound Control</h3>
            <div class="styleguide-grid">
                <?= Component::render('SoundControl', ['state' => 'unmuted'])?>
                <?= Component::render('SoundControl', ['state' => 'muted'])?>
            </div>

            <h3>Music Services</h3>
            <div class="styleguide-grid">
                <?= Component::render('MusicServiceButton', ['service' => 'spotify'])?>
                <?= Component::render('MusicServiceButton', ['service' => 'deezer'])?>
                <?= Component::render('MusicServiceButton', ['service' => 'apple'])?>
                <?= Component::render('MusicServiceButton', ['service' => 'youtube'])?>
                <?= Component::render('MusicServiceButton', ['service' => 'amazon'])?>
            </div>
        </section>

        <section class="styleguide-section">
            <h2>Footer</h2>
            <div
                style="background: var(--color-neutral-100); border: 1px solid var(--color-neutral-200); padding: 20px;">
                <p style="font-size: 12px; margin-bottom: 8px; color: var(--color-neutral-500);">Desktop Version</p>
                <?= Component::render('Footer')?>

                <p style="font-size: 12px; margin-top: 32px; margin-bottom: 8px; color: var(--color-neutral-500);">
                    Mobile Version</p>
                <div class="force-mobile" style="height: auto; min-height: 400px;">
                    <?= Component::render('Footer')?>
                </div>
            </div>
        </section>
    </main>

    <?= Component::render('Footer')?>

    <script src="assets/js/youtube-modal.js"></script>
</body>

</html>