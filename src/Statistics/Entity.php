<?php

namespace App\Statistics;

use App\Database\Utils\LoadableEntity;
use JetBrains\PhpStorm\ArrayShape;

class Entity
{
    #[ArrayShape([
        'files' => "int",
        'galleries' => "int",
        'simplePages' => "int",
        'segmentPages' => "int",
        'forms' => "int"
    ])] public function getEntityShare(): array
    {
        $fileCount = 'SELECT COUNT(*) FROM file';
        $galleryCount = 'SELECT COUNT(*) FROM gallery';
        $simplePageCount = 'SELECT COUNT(*) FROM page';
        $segmentPageCount = 'SELECT COUNT(*) FROM segment_page';
        $formCount = 'SELECT COUNT(*) FROM form';

        return [
            'files' => LoadableEntity::fetchColumn($fileCount),
            'galleries' => LoadableEntity::fetchColumn($galleryCount),
            'simplePages' => LoadableEntity::fetchColumn($simplePageCount),
            'segmentPages' => LoadableEntity::fetchColumn($segmentPageCount),
            'forms' => LoadableEntity::fetchColumn($formCount),
        ];
    }
}