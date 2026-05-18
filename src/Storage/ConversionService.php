<?php

namespace Jinya\Cms\Storage;

use Jinya\Cms\Configuration\JinyaConfiguration;
use Jinya\Cms\Logging\Logger;
use Psr\Log\LoggerInterface;
use Throwable;

readonly class ConversionService
{
    private LoggerInterface $logger;

    public function __construct()
    {
        $this->logger = Logger::getLogger();
    }

    /**
     * @return ImageConversionService
     */
    private function getConverter(): ImageConversionService
    {
        $this->logger->debug('Get image conversion service');
        if (JinyaConfiguration::getConfiguration()->get('image_converter', 'jinya', 'intervention') === 'imaginary') {
            $this->logger->debug('Configured is imaginary, return an ImaginaryConversionService');
            return new ImaginaryConversionService();
        }

        if (extension_loaded('ffi')) {
            $this->logger->debug('ffi is available, return a VipsConversionService');
            try {
                return new VipsConversionService();
            } catch (Throwable) {
                $this->logger->warning(
                    'ffi is available, but vips is not installed correctly, use intervention instead'
                );
                return new InterventionConversionService();
            }
        }

        if (extension_loaded('imagick') || extension_loaded('gd')) {
            $this->logger->debug('Imagick, GD or ffi are available, return an InterventionConversionService');
            return new InterventionConversionService();
        }

        $this->logger->debug('No specific conversion service is available, return an NoopConversionService');
        return new NoopConversionService();
    }

    public function convertFile(int $id): void
    {
        $conversionService = $this->getConverter();
        $conversionService->convertFile($id);
    }
}
