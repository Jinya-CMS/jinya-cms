<?php

namespace App\Theming;

class Engine
{
    public static function getPlatesEngine(): \League\Plates\Engine
    {
        $engine = new \League\Plates\Engine();
        $engine->addFolder('mailing', __ROOT__ . '/src/Mailing/Templates');
        $engine->addFolder('emergency', __ROOT__ . '/src/Emergency/Templates');
        $engine->setFileExtension('phtml');

        return $engine;
    }
}