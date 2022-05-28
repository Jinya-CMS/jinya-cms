<?php

namespace App\Web\Actions\File;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\File;
use App\Storage\StorageBaseService;
use App\Utils\ImageManipulator;
use App\Utils\ImageType;
use App\Web\Actions\Action;
use Imagick;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpNotFoundException;

/**
 *
 */
class GetFileContentAction extends Action
{
    /**
     * {@inheritDoc}
     * @throws HttpNotFoundException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     * @throws NoResultException
     * @throws \ImagickException
     */
    protected function action(): Response
    {
        $fileId = $this->args['id'];
        $file = File::findById($fileId);

        if ($file === null) {
            throw new HttpNotFoundException($this->request, 'File not found');
        }

        if (!extension_loaded('imagick')) {
            return $this->respondFile($file->path, $file->type);
        }

        $imageCompressor = new ImageManipulator();
        $image = false;
        $imageContent = '';
        if (array_key_exists('width', $this->queryParams)) {
            $width = $this->queryParams['width'] ?? null;
            if (!is_null($width)) {
                $image = $imageCompressor->changeImageSize(StorageBaseService::BASE_PATH . '/public/' . $file->path, $width);
            }
        }
        if (!$image) {
            $image = new Imagick(StorageBaseService::BASE_PATH . '/public/' . $file->path);
        }
        if ($image instanceof Imagick) {
            $type = $this->queryParams['type'] ?? 'webp';
            $targetType = match (strtolower($type)) {
                'png' => ImageType::Png,
                'jpg' => ImageType::Jpg,
                'gif' => ImageType::Gif,
                'bmp' => ImageType::Bmp,
                default => ImageType::Webp
            };
            $contentType = match ($targetType) {
                ImageType::Webp => 'image/webp',
                ImageType::Png => 'image/png',
                ImageType::Jpg => 'image/jpeg',
                ImageType::Gif => 'image/gif',
                ImageType::Bmp => 'image/bmp',
            };

            $imageContent = $imageCompressor->convertImage($image, $targetType) ?: '';

            $this->response->getBody()->write($imageContent);

            return $this->response
                ->withHeader('Content-Type', $contentType)
                ->withStatus(200);
        }

        return $this->respondFile($file->path, $file->type);
    }
}
