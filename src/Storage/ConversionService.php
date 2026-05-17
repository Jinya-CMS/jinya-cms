<?php

namespace Jinya\Cms\Storage;

use Jinya\Cms\Configuration\JinyaConfiguration;
use Jinya\Cms\Logging\Logger;
use Psr\Log\LoggerInterface;

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
        if (JinyaConfiguration::getConfiguration()->get('image_converter', 'jinya', 'imagick') === 'imaginary') {
            $this->logger->debug('Configured is imaginary, return an ImaginaryConversionService');
            return new ImaginaryConversionService();
        }

        $this->logger->debug('No specific conversion service is configured, return an ImagickConversionService');
        return new ImagickConversionService();
    }

    public function convertFile(int $id): void
    {
        $conversionService = $this->getConverter();
        $conversionService->convertFile($id);
    }
}
