<?php

namespace Jinya\Services\Media;

interface ConversionServiceInterface
{
    /**
     * @param string $data
     * @param int $targetType
     * @return resource
     */
    public function convertImage(string $data, int $targetType);

    /**
     * @return array
     */
    public function getSupportedTypes(): array;
}