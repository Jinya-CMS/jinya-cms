<?php

declare(strict_types=1);

use Jinya\Cms\Configuration\JinyaConfiguration;
use Jinya\Cms\Database\File;
use Jinya\Cms\Storage\ConversionService;
use Jinya\Cms\Storage\StorageBaseService;
use Jinya\Cms\Theming\Extensions\FileExtension;
use Jinya\Cms\Utils\ImageType;
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

$id = (int)$id;

$width = $queryParams['width'] ?? JinyaConfiguration::getConfiguration()->get(
    'default_width',
    'image_cache',
    FileExtension::RESOLUTIONS_FOR_SOURCE[2]
);
$type = $queryParams['type'] ?? JinyaConfiguration::getConfiguration()->get(
    'default_type',
    'image_cache',
    (empty(Imagick::queryFormats('WEBP')) ? ImageType::Jpg : ImageType::Webp)->string()
);
$file = File::findById($id);
if ($file === null) {
    exit(404);
}

$fullpath = StorageBaseService::BASE_PATH . '/public/' . $file->path . '-' . $width . 'w.' . $type;
if (file_exists($fullpath)) {
    header('Location: ' . $file->path . '-' . $width . 'w.' . $type);
    exit(302);
}

try {
    $conversionService = new ConversionService();
    $conversionService->convertFile($id);
} catch (Throwable $exception) {
    exit(500);
}

header('Location: ' . $file->path . '-' . $width . 'w.' . $type);
exit(302);
