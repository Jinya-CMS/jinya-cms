<?php

namespace Jinya\Cms\Storage;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use Jinya\Cms\Configuration\JinyaConfiguration;
use Jinya\Cms\Database\Exceptions\EmptyResultException;
use Jinya\Cms\Database\File;
use Jinya\Cms\Logging\Logger;
use Jinya\Cms\Theming\Extensions\FileExtension;
use Jinya\Cms\Utils\ImageType;
use Psr\Log\LoggerInterface;
use Throwable;

readonly class ImaginaryConversionService extends ImageConversionService
{
    private LoggerInterface $logger;

    public function __construct()
    {
        $this->logger = Logger::getLogger();
    }

    public function convertFile(int $id): void
    {
        $this->logger->debug('Get file from database', ['fileId' => $id]);
        $file = File::findById($id);
        if ($file === null) {
            $this->logger->warning('File does not exist', ['fileId' => $id]);
            throw new EmptyResultException('The file was not found');
        }

        $this->logger->info('Process file', ['fileId' => $id]);
        $imageTypes = ImageType::cases();
        foreach (FileExtension::RESOLUTIONS_FOR_SOURCE as $width) {
            foreach ($imageTypes as $imageType) {
                try {
                    $this->logger->debug('Call imaginary to convert image', ['fileId' => $id]);
                    $this->cacheImage($file, $width, $imageType);
                } catch (Throwable $exception) {
                    $this->logger->error('Failed to convert file', ['fileId' => $id, 'exception' => $exception]);
                }
            }
        }
    }

    /**
     * @throws EmptyResultException
     */
    private function cacheImage(File $file, int $width, ImageType $imageType): void
    {
        $imaginaryServer = JinyaConfiguration::getConfiguration()->get('imaginary_url', 'jinya');
        $type = match ($imageType) {
            ImageType::Webp, ImageType::Png => $imageType->string(),
            ImageType::Jpg => 'jpeg',
        };
        $httpClient = new Client();
        $req = new Request(
            'post',
            "$imaginaryServer/resize?width=$width&type=" . $type,
            ['Content-Type' => 'image/' . $type],
            fopen(StorageBaseService::BASE_PATH . '/public/' . $file->path, 'rb+') ?: throw new EmptyResultException(
                'The file was not found'
            )
        );
        try {
            $this->logger->info(
                'Convert file using imaginary',
                ['fileId' => $file->id, 'width' => $width, 'fileType' => $imageType]
            );
            $res = $httpClient->send($req);
            $res->getBody()->rewind();
            $this->logger->info(
                'File cached for',
                ['fileId' => $file->id, 'width' => $width, 'fileType' => $imageType]
            );
            file_put_contents($this->getImagePath($file, $imageType, $width), $res->getBody()->getContents());
        } catch (GuzzleException $exception) {
            $this->logger->error(
                'Imaginary error',
                ['fileId' => $file->id, 'width' => $width, 'fileType' => $imageType, 'exception' => $exception]
            );
        } catch (Throwable $exception) {
            $this->logger->error(
                'Failed to convert file',
                ['fileId' => $file->id, 'width' => $width, 'fileType' => $imageType, 'exception' => $exception]
            );
        }
    }
}
