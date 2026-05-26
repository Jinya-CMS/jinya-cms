<?php

namespace Jinya\Cms\Storage;

use Jcupitt\Vips\FFI;
use Jcupitt\Vips\Image;
use Jcupitt\Vips\Kernel;
use Jinya\Cms\Database\Exceptions\EmptyResultException;
use Jinya\Cms\Database\File;
use Jinya\Cms\Logging\Logger;
use Jinya\Cms\Theming\Extensions\FileExtension;
use Jinya\Cms\Utils\ImageType;
use Psr\Log\LoggerInterface;
use Throwable;

class VipsConversionService extends ImageConversionService
{
    private readonly LoggerInterface $logger;


    public function __construct()
    {
        $this->logger = Logger::getLogger();
        FFI::version();
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
        $image = Image::newFromFile(StorageBaseService::BASE_PATH . '/public/' . $file->path);
        foreach ($reversedResolution as $width) {
            $targetScale = min($width / $image->width, 1.0);
            $scaledImage = $image->resize($targetScale, [
                'kernel' => Kernel::LANCZOS3,
            ]);
            foreach ($imageTypes as $imageType) {
                try {
                    $this->cacheFile($scaledImage, $file, $width, $imageType);
                } catch (Throwable $exception) {
                    $this->logger->error('Failed to convert file', ['fileId' => $id, 'exception' => $exception]);
                }
            }
        }
    }

    private function cacheFile(Image $image, File $file, int $width, ImageType $imageType): void
    {
        try {
            $fileType = $imageType->string();
            $this->logger->info('Cache file', ['fileId' => $file->id, 'width' => $width, 'fileType' => $fileType]);
            $image->writeToFile($this->getImagePath($file, $imageType, $width));
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
