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
        $file = File::findById($id);
        if ($file === null) {
            throw new EmptyResultException('The file was not found');
        }

        $this->logger->info("Process file $file->name");
        $imageTypes = ImageType::cases();
        foreach (FileExtension::RESOLUTIONS_FOR_SOURCE as $width) {
            foreach ($imageTypes as $imageType) {
                try {
                    $this->cacheImage($file, $width, $imageType);
                } catch (Throwable $exception) {
                    $this->logger->error($exception->getMessage());
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
            $res = $httpClient->send($req);
            $res->getBody()->rewind();
            $this->logger->info("{$file->name}: File cached for " . $imageType->string() . " in resolution $width");
            file_put_contents($this->getImagePath($file, $imageType, $width), $res->getBody()->getContents());
        } catch (GuzzleException $exception) {
            $this->logger->error("{$file->name}: Failed to convert file to " . $imageType->string());
            $this->logger->error("Imaginary returned: " . $exception->getMessage());
        } catch (Throwable $exception) {
            $this->logger->error("{$file->name}: Failed to convert file to " . $imageType->string());
        }
    }
}
