<?php

namespace Jinya\Cms\Storage;

use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\DriverInterface;
use Intervention\Image\Interfaces\ImageInterface;
use Intervention\Image\Interfaces\ImageManagerInterface;
use Jinya\Cms\Database\Exceptions\EmptyResultException;
use Jinya\Cms\Database\File;
use Jinya\Cms\Logging\Logger;
use Jinya\Cms\Theming\Extensions\FileExtension;
use Jinya\Cms\Utils\ImageType;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Throwable;

class InterventionConversionService extends ImageConversionService
{
    private readonly LoggerInterface $logger;
    private readonly ImageManagerInterface $imageManager;

    private static ?DriverInterface $driver = null;

    public function __construct()
    {
        $this->logger = Logger::getLogger();
        if (self::$driver === null) {
            if (extension_loaded('imagick')) {
                $this->logger->info('Using driver imagick');
                self::$driver = new ImagickDriver();
            } elseif (extension_loaded('gd')) {
                $this->logger->info('Using driver gd');
                self::$driver = new GdDriver();
            }
            if (self::$driver === null) {
                throw new RuntimeException('No driver available');
            }
        }

        $this->imageManager = ImageManager::usingDriver(self::$driver);
    }

    /**
     * @throws EmptyResultException
     */
    public function convertFile(int $id): void
    {
        $this->logger->debug('Get file from database', ['fileId' => $id]);
        $file = File::findById($id);
        if ($file === null) {
            $this->logger->warning('File does not exist', ['fileId' => $id]);
            throw new EmptyResultException('The file was not found');
        }

        $this->logger->info("Process file $file->name");
        $imageTypes = ImageType::cases();
        $reversedResolution = array_reverse(FileExtension::RESOLUTIONS_FOR_SOURCE);
        $this->logger->debug('Call intervention to convert image', ['fileId' => $id]);
        foreach ($reversedResolution as $width) {
            foreach ($imageTypes as $imageType) {
                try {
                    $image = $this->imageManager->decodePath(StorageBaseService::BASE_PATH . '/public/' . $file->path);
                    $scaledImage = $image->scaleDown($width);
                    $this->cacheFile($scaledImage, $file, $width, $imageType);
                } catch (Throwable $exception) {
                    $this->logger->error('Failed to convert file', ['fileId' => $id, 'exception' => $exception]);
                }
            }
        }
    }

    private function cacheFile(ImageInterface $image, File $file, int $width, ImageType $imageType): void
    {
        try {
            $fileType = $imageType->string();
            $this->logger->info('Cache file', ['fileId' => $file->id, 'width' => $width, 'fileType' => $fileType]);
            $image->save($this->getImagePath($file, $imageType, $width));
            $this->logger->info(
                'File cache generated successfully',
                ['fileId' => $file->id, 'width' => $width, 'fileType' => $fileType]
            );
        } catch (Throwable $exception) {
            $fileType = $imageType->string();
            $this->logger->error(
                'Failed to convert file',
                ['fileId' => $file->id, 'width' => $width, 'fileType' => $fileType, 'exception' => $exception]
            );
        }
    }
}
