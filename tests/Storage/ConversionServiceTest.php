<?php

namespace Jinya\Cms\Storage;

use Faker\Provider\Uuid;
use Jinya\Cms\Database\Exceptions\EmptyResultException;
use Jinya\Cms\Database\File;
use Jinya\Cms\Tests\DatabaseAwareTestCase;
use Jinya\Cms\Theming\Extensions\FileExtension;
use Jinya\Cms\Utils\ImageType;

class ConversionServiceTest extends DatabaseAwareTestCase
{
    public function testConvertFile(): void
    {
        $tmpFileName = Uuid::uuid();
        $tmpPath = StorageBaseService::BASE_PATH . '/public/' . $tmpFileName;
        copy('https://picsum.photos/4320/2160', $tmpPath);
        $file = new File();
        $file->path = $tmpFileName;
        $file->name = 'Testimage';
        $file->type = (string)mime_content_type($tmpPath);
        $file->create();

        $conversionService = new ConversionService();
        $conversionService->convertFile($file->id);

        foreach (FileExtension::RESOLUTIONS_FOR_SOURCE as $width) {
            foreach (ImageType::cases() as $case) {
                $path = "{$tmpPath}-{$width}w.{$case->string()}";
                self::assertFileExists($path);
                unlink($path);
            }
        }

        unlink($tmpPath);
    }

    public function testConvertFileFileNotExists(): void
    {
        $this->expectException(EmptyResultException::class);
        $conversionService = new ConversionService();
        $conversionService->convertFile(-1);
    }
}
