<?php

namespace Jinya\Cms\Theming\Extensions;

use Jinya\Cms\Console\FileCacheCommand;
use Jinya\Cms\Database\File;
use Jinya\Cms\Storage\StorageBaseService;
use Jinya\Cms\Tests\DatabaseAwareTestCase;
use Jinya\Cms\Theming\Engine;
use Jinya\Cms\Utils\ImageType;
use Faker\Provider\Uuid;

class FileExtensionTest extends DatabaseAwareTestCase
{
    private FileExtension $extension;

    public function testRegister(): void
    {
        $engine = Engine::getPlatesEngine();
        $this->extension->register($engine);
        self::assertTrue($engine->functions->exists('pictureSources'));
        self::assertTrue($engine->functions->exists('sizes'));
        self::assertTrue($engine->functions->exists('srcset'));
    }

    public function testSrcsetWithImagePhp(): void
    {
        copy('https://picsum.photos/200/300', __ROOT__ . '/tmp/file');
        $file = new File();
        $file->name = Uuid::uuid();
        $file->path = __ROOT__ . '/tmp/file';
        $file->create();

        $image = $this->extension->srcset($file, ImageType::Webp);
        self::assertStringContainsString('image.php', $image);
        self::assertStringContainsString('webp', $image);

        $image = $this->extension->srcset($file, ImageType::Jpg);
        self::assertStringContainsString('image.php', $image);
        self::assertStringContainsString('jpg', $image);

        $image = $this->extension->srcset($file, ImageType::Png);
        self::assertStringContainsString('image.php', $image);
        self::assertStringContainsString('png', $image);

        $image = $this->extension->srcset($file);
        self::assertStringContainsString('image.php', $image);
    }

    public function testSrcsetWithImageFileExists(): void
    {
        $name = Uuid::uuid();
        $path = StorageBaseService::BASE_PATH . '/public/' . $name;
        copy('https://picsum.photos/200/300', $path);
        chmod($path, 0777);
        $file = new File();
        $file->name = Uuid::uuid();
        $file->path = $name;
        $file->create();
        (new FileCacheCommand())->run();

        $image = $this->extension->srcset($file, ImageType::Webp);
        self::assertStringContainsString('.webp', $image);

        $image = $this->extension->srcset($file, ImageType::Jpg);
        self::assertStringContainsString('.jpg', $image);

        $image = $this->extension->srcset($file, ImageType::Png);
        self::assertStringContainsString('.png', $image);
    }

    public function testPictureSourcesWithImagePhp(): void
    {
        copy('https://picsum.photos/200/300', __ROOT__ . '/tmp/file');
        $file = new File();
        $file->name = Uuid::uuid();
        $file->path = __ROOT__ . '/tmp/file';
        $file->create();

        $image = $this->extension->pictureSources(
            $file,
            ImageType::Webp,
            ImageType::Jpg,
            ImageType::Png
        );
        self::assertStringContainsString('image.php', $image);
        self::assertStringContainsString('webp', $image);
        self::assertStringContainsString('jpg', $image);
        self::assertStringContainsString('png', $image);
    }

    public function testSizes(): void
    {
        self::assertEquals('100vw', $this->extension->sizes());
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->extension = new FileExtension();
    }
}
