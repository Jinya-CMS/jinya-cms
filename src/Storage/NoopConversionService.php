<?php

namespace Jinya\Cms\Storage;

use Jinya\Cms\Database\File;
use Jinya\Cms\Logging\Logger;
use Jinya\Cms\Theming\Extensions\FileExtension;
use Jinya\Cms\Utils\ImageType;
use Psr\Log\LoggerInterface;
use Throwable;

class NoopConversionService extends ImageConversionService
{
    private LoggerInterface $logger;

    public function __construct()
    {
        $this->logger = Logger::getLogger();
    }

    public function convertFile(int $id): void
    {
        $this->logger->info("Process file $id with NoopConversionService");
        $file = File::findById($id);
        $imageTypes = ImageType::cases();
        $originalPath = StorageBaseService::SAVE_PATH . $file->path;
        foreach (FileExtension::RESOLUTIONS_FOR_SOURCE as $width) {
            foreach ($imageTypes as $imageType) {
                try {
                    $path = $this->getImagePath($file, $imageType, $width);
                    $this->logger->debug(
                        'Simply copy the file over',
                        [
                            'fileId' => $file->id,
                            'targetPath' => $path,
                            'originalPath' => $originalPath
                        ]
                    );
                    copy($originalPath, $path);
                } catch (Throwable $exception) {
                    $this->logger->error($exception->getMessage());
                }
            }
        }
    }
}
