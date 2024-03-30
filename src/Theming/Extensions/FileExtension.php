<?php

namespace Jinya\Cms\Theming\Extensions;

use Jinya\Cms\Database\File;
use Jinya\Cms\Storage\StorageBaseService;
use Jinya\Cms\Utils\ImageType;
use Jinya\Plates\Engine;
use Jinya\Plates\Extension\BaseExtension;
use RuntimeException;

/**
 * Provides extensions to the Plates engine, adding helper methods for image handling of files
 */
class FileExtension extends BaseExtension
{
    /** @var int[] The default resolutions supported by Jinya CMS */
    public const RESOLUTIONS_FOR_SOURCE = [480, 720, 1080, 2160, 4320];
    /** @var string The default sizes property supported by Jinya CMS */
    private string $sizesAsString = '100vw';

    /**
     * Registers the helper method with the plates engine
     *
     * @param Engine $engine
     * @return void
     */
    public function register(Engine $engine): void
    {
        $engine->functions->add('pictureSources', [$this, 'pictureSources']);
        $engine->functions->add('sizes', [$this, 'sizes']);
        $engine->functions->add('srcset', [$this, 'srcset']);
    }

    /**
     * Generates a srcset compatible string for use in img and picture tags
     *
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
                $sources[] = "/image.php?id=$file->id&width=$width {$width}w";
            } else {
                $type = match ($imageType) {
                    ImageType::Webp => 'webp',
                    ImageType::Png => 'png',
                    ImageType::Jpg => 'jpg',
                };
                $fullpath = StorageBaseService::BASE_PATH . '/public/' . $file->path . '-' . $width . 'w.' . $type;
                if (file_exists($fullpath)) {
                    $webpath = "$file->path-{$width}w.$type";
                    $sources[] = "$webpath {$width}w";
                } else {
                    $sources[] = "/image.php?id=$file->id&width=$width&type=$type {$width}w";
                }
            }
        }

        return implode(",\n", array_reverse($sources));
    }

    /**
     * Returns the sizes supported by the srcset method
     *
     * @return string
     */
    public function sizes(): string
    {
        return $this->sizesAsString;
    }

    /**
     * Generates source tags for use in a picture element
     *
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
            };
            $typeAsString = match ($item) {
                ImageType::Webp => 'webp',
                ImageType::Png => 'png',
                ImageType::Jpg => 'jpg',
            };
            foreach (self::RESOLUTIONS_FOR_SOURCE as $width) {
                $sources[] = "<source srcset='/image.php?id=$file->id&width=$width&type=$typeAsString' media='(min-width: {$width}px)' type='$type'>";
            }
        }

        return implode(PHP_EOL, $sources);
    }
}
