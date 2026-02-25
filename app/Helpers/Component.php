<?php

namespace App\Helpers;

class Component
{
    public static function render($componentName, $props = [])
    {
        $componentFile = __DIR__ . '/../Views/Components/' . $componentName . '.php';

        if (file_exists($componentFile)) {
            // Extract props to make them available as variables in the component view
            extract($props);

            // Start buffering output
            ob_start();
            require $componentFile;
            return ob_get_clean();
        }
        else {
            return "<!-- Component $componentName not found -->";
        }
    }
}