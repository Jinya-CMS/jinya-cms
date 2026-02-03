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

require_once __DIR__ . '/../images.php';

handle_images();
