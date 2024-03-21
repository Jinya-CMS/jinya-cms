<?php

namespace Jinya\Tests\Theming\Extensions;

use App\Console\FileCacheCommand;
use App\Database\File;
use App\Storage\StorageBaseService;
use App\Tests\DatabaseAwareTestCase;
use App\Theming\Engine;
use App\Theming\Extensions\FileExtension;
use App\Utils\ImageType;
use Faker\Provider\Uuid;

class FileExtensionTest extends DatabaseAwareTestCase
{
    private FileExtension $extension;

    public function testRegister(): void
    {
        $engine = Engine::getPlatesEngine();
        $this->extension->register($engine);
        self::assertTrue($engine->doesFunctionExist('pictureSources'));
        self::assertTrue($engine->doesFunctionExist('sizes'));
        self::assertTrue($engine->doesFunctionExist('srcset'));
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

        $image = $this->extension->srcset($file, ImageType::Gif);
        self::assertStringContainsString('image.php', $image);
        self::assertStringContainsString('gif', $image);

        $image = $this->extension->srcset($file, ImageType::Bmp);
        self::assertStringContainsString('image.php', $image);
        self::assertStringContainsString('bmp', $image);

        $image = $this->extension->srcset($file);
        self::assertStringContainsString('image.php', $image);
    }

    public function testSrcsetWithImageFileExists(): void
    {
        $name = Uuid::uuid();
        $path = StorageBaseService::BASE_PATH . '/public/' . $name;
        copy('https://via.placeholder.com/300/09f/fff.png', $path);
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

        $image = $this->extension->pictureSources($file, ImageType::Webp, ImageType::Bmp, ImageType::Jpg, ImageType::Gif, ImageType::Png);
        self::assertStringContainsString('image.php', $image);
        self::assertStringContainsString('webp', $image);
        self::assertStringContainsString('jpg', $image);
        self::assertStringContainsString('png', $image);
        self::assertStringContainsString('gif', $image);
        self::assertStringContainsString('bmp', $image);
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
