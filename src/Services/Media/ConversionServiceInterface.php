<?php

namespace Jinya\Services\Media;

interface ConversionServiceInterface
{
    /**
     * @return resource
     */
    public function convertImage(string $data, int $targetType);

    public function getSupportedTypes(): array;
}
