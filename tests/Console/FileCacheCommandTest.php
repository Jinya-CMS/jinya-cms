<?php

namespace Jinya\Cms\Console;

use Faker\Provider\Uuid;
use Jinya\Cms\Database\File;
use Jinya\Cms\Storage\StorageBaseService;
use Jinya\Cms\Tests\DatabaseAwareTestCase;
use Jinya\Cms\Theming\Extensions\FileExtension;
use Jinya\Cms\Utils\ImageType;
use Jinya\Cms\Utils\UuidGenerator;

class FileCacheCommandTest extends DatabaseAwareTestCase
{
    public function testRun(): void
    {
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

        $command = new FileCacheCommand();
        $command->run();

        foreach (FileExtension::RESOLUTIONS_FOR_SOURCE as $width) {
            foreach (ImageType::cases() as $case) {
                $path = "{$tmpPath}-{$width}w.{$case->string()}";
                self::assertFileExists($path);
                @unlink($path);
            }
        }

        unlink($tmpPath);
    }

    public function testRunFileNotExists(): void
    {
        $fileName = UuidGenerator::generateV4();
        $file = new File();
        $file->path = $fileName;
        $file->name = 'Testimage';
        $file->type = 'image/png';
        $file->create();

        $command = new FileCacheCommand();
        $command->run();
        self::assertTrue(true);
    }
}
