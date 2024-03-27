<?php

namespace App\Theming;

use Jinya\Plates\Engine as PlatesEngine;

/**
 * Helper class to create a new Plates engine
 */
abstract class Engine
{
    /**
     * Creates a new Plates engine and registers the default routes
     *
     * @return PlatesEngine
     */
    public static function getPlatesEngine(): PlatesEngine
    {
        $engine = new PlatesEngine();
        $engine->addFolder('mailing', __ROOT__ . '/src/Mailing/Templates');
        $engine->addFolder('emergency', __ROOT__ . '/src/Emergency/Templates');

        return $engine;
    }
}
