<?php

namespace Jinya\Cms\Storage;

use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;
use Intervention\Image\Interfaces\ImageManagerInterface;
use Jinya\Cms\Database\Exceptions\EmptyResultException;
use Jinya\Cms\Database\File;
use Jinya\Cms\Logging\Logger;
use Jinya\Cms\Theming\Extensions\FileExtension;
use Jinya\Cms\Utils\ImageType;
use Psr\Log\LoggerInterface;
use Throwable;

readonly class ImagickConversionService extends ImageConversionService
{
    private LoggerInterface $logger;
    private ImageManagerInterface $imageManager;

    public function __construct()
    {
        $this->logger = Logger::getLogger();
        $this->imageManager = ImageManager::usingDriver(new Driver());
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
        foreach (FileExtension::RESOLUTIONS_FOR_SOURCE as $width) {
            foreach ($imageTypes as $imageType) {
                try {
                    $this->logger->debug('Call imagick to convert image', ['fileId' => $id]);
                    $image = $this->imageManager->decodePath(StorageBaseService::BASE_PATH . '/public/' . $file->path);
                    $this->cacheFile($image->scale($width), $file, $width, $imageType);
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
