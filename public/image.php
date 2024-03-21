<?php

declare(strict_types=1);

use App\Database\File;
use App\Storage\StorageBaseService;
use App\Utils\AppSettingsInitializer;
use Intervention\Image\ImageManager;
use Slim\Factory\ServerRequestCreatorFactory;

require __DIR__ . '/../defines.php';
require __ROOT__ . '/vendor/autoload.php';

AppSettingsInitializer::loadDotEnv();
$serverRequestCreator = ServerRequestCreatorFactory::create();
$request = $serverRequestCreator->createServerRequestFromGlobals();

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
    'jpg' => $image->toJpg(),
    'gif' => $image->toGif(),
    'bmp' => $image->toBmp(),
    default => $image->toWebp(),
};
$encodedImage->save($fullpath);

header('Content-Type: ' . $encodedImage->mediaType());
echo (string)$encodedImage;
