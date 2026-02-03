<?php

namespace Jinya\Cms\Storage;

use Faker\Provider\Uuid;
use Jinya\Cms\Database\Exceptions\EmptyResultException;
use Jinya\Cms\Database\File;
use Jinya\Cms\Tests\DatabaseAwareTestCase;
use Jinya\Cms\Theming\Extensions\FileExtension;
use Jinya\Cms\Utils\ImageType;

class ImaginaryConversionServiceTest extends DatabaseAwareTestCase
{
    public function testConvertFile(): void
    {
        if (getenv('CI') === 'true') {
            self::markTestSkipped('Skipping test on CI since imaginary not working properly');
        }

        $tmpFileName = Uuid::uuid();
        $tmpPath = StorageBaseService::BASE_PATH . '/public/' . $tmpFileName;
        $res = @copy(__DIR__ . '/../files/test-image.webp', $tmpPath);
        if (!$res) {
            self::fail('Could not copy file');
        }
        $file = new File();
        $file->path = $tmpFileName;
        $file->name = 'Testimage';
        $file->type = (string)mime_content_type($tmpPath);
        $file->create();

        $conversionService = new ImaginaryConversionService();
        $conversionService->convertFile($file->id);

        foreach (FileExtension::RESOLUTIONS_FOR_SOURCE as $width) {
            foreach (ImageType::cases() as $case) {
                $path = "$tmpPath-{$width}w.{$case->string()}";
                self::assertFileExists($path);
                unlink($path);
            }
        }

        unlink($tmpPath);
    }

    public function testConvertFileFileNotExists(): void
    {
        if (getenv('CI') === 'true') {
            self::markTestSkipped('Skipping test on CI since imaginary not working properly');
        }

        $this->expectException(EmptyResultException::class);
        $conversionService = new ConversionService();
        $conversionService->convertFile(-1);
    }
}
