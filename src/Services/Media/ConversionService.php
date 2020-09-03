<?php

namespace Jinya\Services\Media;

use Imagick;
use ImagickException;
use Psr\Log\LoggerInterface;

class ConversionService implements ConversionServiceInterface
{
    /** @var LoggerInterface */
    private LoggerInterface $logger;

    /**
     * ConversionService constructor.
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return resource
     */
    public function convertImage(string $data, int $targetType)
    {
        $this->logger->debug('Start image conversion');
        $returnData = null;
        if ($targetType >= 0) {
            if (extension_loaded('imagick')) {
                $returnData = $this->imagickConvertImage($data, $targetType);
            } elseif (extension_loaded('gd')) {
                $returnData = $this->gdConvertImage($data, $targetType);
            }
        }

        $returnStream = fopen('php://temp', 'wrb+');
        fwrite($returnStream, empty($returnData) ? $data : $returnData);
        fflush($returnStream);
        rewind($returnStream);

        $this->logger->debug('Finished image conversion');

        return $returnStream;
    }

    private function imagickConvertImage(string $data, int $targetType): ?string
    {
        $this->logger->debug('Convert file using imagick');
        $inputFile = tmpfile();

        try {
            fwrite($inputFile, $data);
            fflush($inputFile);
            rewind($inputFile);

            $inputPath = stream_get_meta_data($inputFile)['uri'];

            try {
                $inputImage = new Imagick($inputPath);
                $outputImage = new Imagick();

                foreach ($inputImage as $image) {
                    $outputImage->addImage($image);
                }

                $mergedImage = $outputImage->flattenImages();
                switch ($targetType) {
                    case IMAGETYPE_BMP:
                        $mergedImage->setFormat('bmp');

                        break;
                    case IMAGETYPE_GIF:
                        $mergedImage->setFormat('gif');

                        break;
                    case IMAGETYPE_PNG:
                        $mergedImage->setFormat('png');

                        break;
                    case IMAGETYPE_JPEG:
                        $mergedImage->setFormat('jpg');

                        break;
                    case IMAGETYPE_WEBP:
                        $mergedImage->setFormat('webp');

                        break;
                }

                $mergedImage->writeImage($inputPath);

                return file_get_contents($inputPath);
            } catch (ImagickException $e) {
                $this->logger->error($e->getMessage());
                $this->logger->error($e->getTraceAsString());
            }
        } finally {
            fclose($inputFile);
        }

        return null;
    }

    private function gdConvertImage(string $data, int $targetType): ?string
    {
        $this->logger->debug('Convert file using gd');
        if (imagetypes() & $targetType) {
            $imageData = @imagecreatefromstring($data);

            if (!empty($imageData)) {
                ob_start();
                switch ($targetType) {
                    case IMAGETYPE_BMP:
                        imagebmp($imageData);

                        break;
                    case IMAGETYPE_GIF:
                        imagegif($imageData);

                        break;
                    case IMAGETYPE_PNG:
                        imagepng($imageData);

                        break;
                    case IMAGETYPE_JPEG:
                        imagejpeg($imageData);

                        break;
                    case IMAGETYPE_WEBP:
                        imagewebp($imageData);

                        break;
                }
                imagedestroy($imageData);

                $image = ob_get_contents();
                ob_clean();

                return $image;
            }
        }

        return null;
    }

    public function getSupportedTypes(): array
    {
        $imageTypes = [];

        if (imagetypes() & IMAGETYPE_BMP) {
            $imageTypes[] = [IMAGETYPE_BMP => 'Bitmap'];
        }
        if (imagetypes() & IMAGETYPE_JPEG) {
            $imageTypes[] = [IMAGETYPE_JPEG => 'JPEG'];
        }
        if (imagetypes() & IMAGETYPE_PNG) {
            $imageTypes[] = [IMAGETYPE_PNG => 'PNG'];
        }
        if (imagetypes() & IMAGETYPE_GIF) {
            $imageTypes[] = [IMAGETYPE_GIF => 'Gif'];
        }
        if (imagetypes() & IMAGETYPE_WEBP) {
            $imageTypes[] = [IMAGETYPE_WEBP => 'WebP'];
        }

        return $imageTypes;
    }
}
