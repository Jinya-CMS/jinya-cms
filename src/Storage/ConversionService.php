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
        $image = $this->imageManager->read(StorageBaseService::BASE_PATH . '/public/' . $file->path);
        $imageTypes = ImageType::cases();
        foreach (FileExtension::RESOLUTIONS_FOR_SOURCE as $width) {
            foreach ($imageTypes as $imageType) {
                $this->cacheFile($image->resize($width), $file, $width, $imageType);
            }
        }
    }

    private function cacheFile(ImageInterface $image, File $file, int $width, ImageType $imageType): void
    {
        $fileType = $imageType->string();
        $this->logger->info("{$file->name}: Create file cache for $fileType and resolution $width");
        $image->save(StorageBaseService::BASE_PATH . "/public/{$file->path}-{$width}w.$fileType");
        $this->logger->info("{$file->name}: File cached for $fileType in resolution $width");
    }
}
