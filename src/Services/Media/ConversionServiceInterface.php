<?php

namespace Jinya\Services\Media;

interface ConversionServiceInterface
{
    /**
     * @param resource $data
     * @param int $targetType
     * @return resource
     */
    public function convertImage($data, int $targetType);

    /**
     * @return array
     */
    public function getSupportedTypes(): array;
}