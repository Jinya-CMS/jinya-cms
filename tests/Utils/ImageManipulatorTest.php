<?php

namespace Jinya\Tests\Utils;

use App\Utils\ImageManipulator;
use App\Utils\ImageType;
use Imagick;
use PHPUnit\Framework\TestCase;

class ImageManipulatorTest extends TestCase
{
    public function testChangeImageSizeWithJpeg(): void
    {
        $compressor = new ImageManipulator();
        $compressedImage = $compressor->changeImageSize(__DIR__ . '/../files/test.jpg', 1024);
        self::assertEquals(1024, $compressedImage->getImageWidth());
    }

    public function testChangeImageSizeWithPng(): void
    {
        $compressor = new ImageManipulator();
        $compressedImage = $compressor->changeImageSize(__DIR__ . '/../files/test.png', 1024);
        self::assertEquals(1024, $compressedImage->getImageWidth());
    }

    public function testChangeImageSizeWithWebp(): void
    {
        $compressor = new ImageManipulator();
        $compressedImage = $compressor->changeImageSize(__DIR__ . '/../files/test.webp', 1024);
        self::assertEquals(1024, $compressedImage->getImageWidth());
    }

    public function testConvertImage(): void
    {
        $compressor = new ImageManipulator();
        $magick = new Imagick(__DIR__ . '/../files/test.webp');
        {
            $changedFormat = $compressor->convertImage($magick, ImageType::Webp);
            $mag = new Imagick();
            $mag->readImageBlob($changedFormat);
            self::assertEquals('image/x-webp', $mag->getImageMimeType());
        }
        {
            $changedFormat = $compressor->convertImage($magick, ImageType::Png);
            $mag = new Imagick();
            $mag->readImageBlob($changedFormat);
            self::assertEquals('image/png', $mag->getImageMimeType());
        }
        {
            $changedFormat = $compressor->convertImage($magick, ImageType::Jpg);
            $mag = new Imagick();
            $mag->readImageBlob($changedFormat);
            self::assertEquals('image/jpeg', $mag->getImageMimeType());
        }
        {
            $changedFormat = $compressor->convertImage($magick, ImageType::Gif);
            $mag = new Imagick();
            $mag->readImageBlob($changedFormat);
            self::assertEquals('image/gif', $mag->getImageMimeType());
        }
        {
            $changedFormat = $compressor->convertImage($magick, ImageType::Bmp);
            $mag = new Imagick();
            $mag->readImageBlob($changedFormat);
            self::assertEquals('image/bmp', $mag->getImageMimeType());
        }
    }

    public function testChangeImageSizeWithTooLarge(): void
    {
        $compressor = new ImageManipulator();
        $compressedImage = $compressor->changeImageSize(__DIR__ . '/../files/test.webp', 9000000);
        self::assertEquals(5504, $compressedImage->getImageWidth());
    }
}
