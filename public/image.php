<?php

declare(strict_types=1);

use Jinya\Cms\Database\File;
use Jinya\Cms\Storage\StorageBaseService;
use Intervention\Image\ImageManager;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;

require __DIR__ . '/../startup.php';

$psr17Factory = new Psr17Factory();
$creator = new ServerRequestCreator(
    $psr17Factory, // ServerRequestFactory
    $psr17Factory, // UriFactory
    $psr17Factory, // UploadedFileFactory
    $psr17Factory  // StreamFactory
);
$request = $creator->fromGlobals();

$queryParams = $request->getQueryParams();
$id = $queryParams['id'] ?? false;
if ($id === false) {
    exit(404);
}

$width = $queryParams['width'] ?? false;
$type = $queryParams['type'] ?? 'webp';
$file = File::findById((int)$id);
if ($file === null) {
    exit(404);
}

$fullpath = StorageBaseService::BASE_PATH . '/public/' . $file->path . '-' . $width . 'w.' . $type;
if (file_exists($fullpath)) {
    header('Location: ' . $file->path . '-' . $width . 'w.' . $type);
    exit(302);
}

$manager = ImageManager::imagick();
$image = $manager->read(StorageBaseService::BASE_PATH . '/public/' . $file->path);
if ($width !== false) {
    $image->scaleDown(width: $width);
}

$encodedImage = match ($type) {
    'png' => $image->toPng(),
    'jpg' => $image->toJpeg(),
    'gif' => $image->toGif(),
    'bmp' => $image->toBitmap(),
    default => $image->toWebp(),
};
$encodedImage->save($fullpath);

header('Content-Type: ' . $encodedImage->mediaType());
echo (string)$encodedImage;
