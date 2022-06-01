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
$type = $queryParams['type'] ?? false;
$file = File::findById((int)$id);
if ($file === null) {
    exit(404);
}

$fullpath = StorageBaseService::BASE_PATH . '/public/' . $file->path . '-' . $width . 'w.' . $type;
if (file_exists($fullpath)) {
    header('Location: ' . $file->path . '-' . $width . 'w.' . $type);
    exit(302);
}

$manager = new ImageManager(['driver' => 'imagick']);
$image = $manager->make(StorageBaseService::BASE_PATH . '/public/' . $file->path);
if ($width !== false) {
    $image->widen($width, fn($image) => $image->upsize());
}

if ($type !== false) {
    $contentType = match (strtolower($type)) {
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'gif' => 'image/gif',
        'bmp' => 'image/bmp',
        default => 'image/webp',
    };
    $targetType = match (strtolower($type)) {
        'png' => 'png',
        'jpg' => 'jpeg',
        'gif' => 'gif',
        'bmp' => 'bmp',
        default => 'webp',
    };
    $image->save($fullpath, format: $targetType);
    echo $image->response($targetType);
} else {
    $image->save($fullpath);
    echo $image->response();
}