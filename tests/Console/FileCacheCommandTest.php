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
        $this->assertTrue(true);
        $this->assertFileExists($tmpPath . '-480w.webp');
        $this->assertFileExists($tmpPath . '-720w.webp');
        $this->assertFileExists($tmpPath . '-1080w.webp');
        $this->assertFileExists($tmpPath . '-2160w.webp');
        $this->assertFileExists($tmpPath . '-4320w.webp');
        $this->assertFileExists($tmpPath . '-480w.png');
        $this->assertFileExists($tmpPath . '-720w.png');
        $this->assertFileExists($tmpPath . '-1080w.png');
        $this->assertFileExists($tmpPath . '-2160w.png');
        $this->assertFileExists($tmpPath . '-4320w.png');
        $this->assertFileExists($tmpPath . '-480w.jpg');
        $this->assertFileExists($tmpPath . '-720w.jpg');
        $this->assertFileExists($tmpPath . '-1080w.jpg');
        $this->assertFileExists($tmpPath . '-2160w.jpg');
        $this->assertFileExists($tmpPath . '-4320w.jpg');
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
        $this->assertTrue(true);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->tmpFileName = UuidGenerator::generateV4();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        try {
            $tmpPath = StorageBaseService::BASE_PATH . '/public/' . $this->tmpFileName;
            @unlink($tmpPath);
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
        } catch (Throwable) {
        }
    }
}
