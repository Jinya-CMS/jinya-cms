<?php

namespace Jinya\Cms\Storage;

use Jinya\Cms\Configuration\JinyaConfiguration;

readonly class ConversionService
{
    /**
     * @return ImageConversionService
     */
    private function getConverter(): ImageConversionService
    {
        if (JinyaConfiguration::getConfiguration()->get('image_converter', 'jinya', 'imagick') === 'imaginary') {
            return new ImaginaryConversionService();
        }

        return new ImagickConversionService();
    }

    public function convertFile(int $id): void
    {
        $conversionService = $this->getConverter();
        $conversionService->convertFile($id);
    }
}
