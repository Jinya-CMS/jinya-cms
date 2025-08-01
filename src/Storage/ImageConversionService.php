<?php

namespace Jinya\Cms\Storage;

use Jinya\Cms\Database\File;
use Jinya\Cms\Utils\ImageType;

abstract readonly class ImageConversionService
{
    abstract public function convertFile(int $id): void;

    protected function getImagePath(File $file, ImageType $type, int $width): string
    {
        return StorageBaseService::BASE_PATH . "/public/$file->path-{$width}w." . $type->string();
    }
}
