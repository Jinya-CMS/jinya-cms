<?php

namespace Jinya\Cms\Storage;

use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;
use Jinya\Cms\Database\Exceptions\EmptyResultException;
use Jinya\Cms\Database\File;
use Jinya\Cms\Logging\Logger;
use Jinya\Cms\Theming\Extensions\FileExtension;
use Jinya\Cms\Utils\ImageType;
use Psr\Log\LoggerInterface;
use Throwable;

readonly class ConversionService
{
    private LoggerInterface $logger;
    private ImageManager $imageManager;

    public function __construct()
    {
        $this->logger = Logger::getLogger();
        $this->imageManager = ImageManager::imagick();
    }

    /**
     * @throws EmptyResultException
     */
    public function convertFile(int $id): void
    {
        $file = File::findById($id);
        if ($file === null) {
            throw new EmptyResultException('The file was not found');
        }

        $this->logger->info("Process file $file->name");
        $imageTypes = ImageType::cases();
        foreach (FileExtension::RESOLUTIONS_FOR_SOURCE as $width) {
            foreach ($imageTypes as $imageType) {
                try {
                    $image = $this->imageManager->read(StorageBaseService::BASE_PATH . '/public/' . $file->path);
                    $this->cacheFile($image->scale($width), $file, $width, $imageType);
                } catch (Throwable $exception) {
                    $this->logger->error($exception->getMessage());
                }
            }
        }
    }

    private function cacheFile(ImageInterface $image, File $file, int $width, ImageType $imageType): void
    {
        try {
            $fileType = $imageType->string();
            $this->logger->info("{$file->name}: Create file cache for $fileType and resolution $width");
            $image->save(StorageBaseService::BASE_PATH . "/public/{$file->path}-{$width}w.$fileType");
            $this->logger->info("{$file->name}: File cached for $fileType in resolution $width");
        } catch (Throwable $exception) {
            $fileType = $imageType->string();
            $this->logger->error("{$file->name}: Failed to convert file to $fileType");
        }
    }
}
