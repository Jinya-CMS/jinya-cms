<?php

namespace App\Theming\Extensions;

use App\Database\File;
use App\Utils\ImageType;
use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;
use RuntimeException;

/**
 *
 */
class FileExtension implements ExtensionInterface
{
    public const RESOLUTIONS_FOR_SOURCE = [480, 720, 1080, 2160, 4320];
    private string $sizesAsString = '100vw';

    /**
     * @inheritDoc
     */
    public function register(Engine $engine): void
    {
        $engine->registerFunction('pictureSources', [$this, 'pictureSources']);
        $engine->registerFunction('sizes', [$this, 'sizes']);
        $engine->registerFunction('srcset', [$this, 'srcset']);
    }

    /**
     * @param File $file
     * @param ImageType|false $imageType
     * @return string
     * @throws RuntimeException
     */
    public function srcset(File $file, ImageType|false $imageType = false): string
    {
        $sources = [];
        foreach (self::RESOLUTIONS_FOR_SOURCE as $width) {
            if ($imageType === false) {
                $sources[] = "/image.php?id=$file->id&width=$width ${width}w";
            } else {
                $type = match ($imageType) {
                    ImageType::Webp => 'webp',
                    ImageType::Png => 'png',
                    ImageType::Jpg => 'jpg',
                    ImageType::Gif => 'gif',
                    ImageType::Bmp => 'bmp',
                    default => throw new RuntimeException('Unexpected match value'),
                };
                $sources[] = "/image.php?id=$file->id&width=$width&type=$type ${width}w";
            }
        }

        return implode(",\n", array_reverse($sources));
    }

    /**
     * @return string
     */
    public function sizes(): string
    {
        return $this->sizesAsString;
    }

    /**
     * @param File $file
     * @param ImageType ...$imageType
     * @return string
     */
    public function pictureSources(File $file, ImageType ...$imageType): string
    {
        $sources = [];
        if (empty($imageType)) {
            $imageType[] = ImageType::Webp;
        }
        foreach ($imageType as $item) {
            $type = match ($item) {
                ImageType::Webp => 'image/webp',
                ImageType::Png => 'image/png',
                ImageType::Jpg => 'image/jpg',
                ImageType::Gif => 'image/gif',
                ImageType::Bmp => 'image/bmp',
            };
            $typeAsString = match ($item) {
                ImageType::Webp => 'webp',
                ImageType::Png => 'png',
                ImageType::Jpg => 'jpg',
                ImageType::Gif => 'gif',
                ImageType::Bmp => 'bmp',
            };
            foreach (self::RESOLUTIONS_FOR_SOURCE as $width) {
                $sources[] = "<source srcset='image.php?id=$file->id&width=$width&type=$typeAsString' media='(min-width: ${width}px)' type='$type'>";
            }
        }

        return implode(PHP_EOL, $sources);
    }
}