<?php

namespace App\Theming;

/**
 * Helper class to create a new Plates engine
 */
abstract class Engine
{
    /**
     * Creates a new Plates engine and registers the default routes
     *
     * @return \League\Plates\Engine
     */
    public static function getPlatesEngine(): \League\Plates\Engine
    {
        $engine = new \League\Plates\Engine();
        $engine->addFolder('mailing', __ROOT__ . '/src/Mailing/Templates');
        $engine->addFolder('emergency', __ROOT__ . '/src/Emergency/Templates');
        $engine->setFileExtension('phtml');

        return $engine;
    }
}
