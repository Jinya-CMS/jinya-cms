<?php

namespace Jinya\Tests\Console;

use App\Console\FileCacheCommand;
use App\Database\File;
use App\Storage\StorageBaseService;
use App\Tests\DatabaseAwareTestCase;
use App\Utils\UuidGenerator;
use Throwable;

class FileCacheCommandTest extends DatabaseAwareTestCase
{
    private string $tmpFileName;

    public function testRun(): void
    {
        $tmpPath = StorageBaseService::BASE_PATH . '/public/' . $this->tmpFileName;
        copy('https://picsum.photos/4320/2160', $tmpPath);
        $file = new File();
        $file->path = $this->tmpFileName;
        $file->name = 'Testimage';
        $file->type = (string)mime_content_type($tmpPath);
        $file->create();

        $command = new FileCacheCommand();
        $command->run();
        self::assertTrue(true);
        self::assertFileExists($tmpPath . '-480w.webp');
        self::assertFileExists($tmpPath . '-720w.webp');
        self::assertFileExists($tmpPath . '-1080w.webp');
        self::assertFileExists($tmpPath . '-2160w.webp');
        self::assertFileExists($tmpPath . '-4320w.webp');
        self::assertFileExists($tmpPath . '-480w.png');
        self::assertFileExists($tmpPath . '-720w.png');
        self::assertFileExists($tmpPath . '-1080w.png');
        self::assertFileExists($tmpPath . '-2160w.png');
        self::assertFileExists($tmpPath . '-4320w.png');
        self::assertFileExists($tmpPath . '-480w.jpg');
        self::assertFileExists($tmpPath . '-720w.jpg');
        self::assertFileExists($tmpPath . '-1080w.jpg');
        self::assertFileExists($tmpPath . '-2160w.jpg');
        self::assertFileExists($tmpPath . '-4320w.jpg');

        // Cleanup the tmp files
        @unlink($tmpPath . '-480w.webp');
        @unlink($tmpPath . '-720w.webp');
        @unlink($tmpPath . '-1080w.webp');
        @unlink($tmpPath . '-2160w.webp');
        @unlink($tmpPath . '-4320w.webp');
        @unlink($tmpPath . '-480w.png');
        @unlink($tmpPath . '-720w.png');
        @unlink($tmpPath . '-1080w.png');
        @unlink($tmpPath . '-2160w.png');
        @unlink($tmpPath . '-4320w.png');
        @unlink($tmpPath . '-480w.jpg');
        @unlink($tmpPath . '-720w.jpg');
        @unlink($tmpPath . '-1080w.jpg');
        @unlink($tmpPath . '-2160w.jpg');
        @unlink($tmpPath . '-4320w.jpg');
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
