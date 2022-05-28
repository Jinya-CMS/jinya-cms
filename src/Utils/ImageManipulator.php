<?php

namespace App\Utils;

use Imagick;
use ImagickException;

/**
 *
 */
class ImageManipulator
{
    /**
     * @param Imagick $image
     * @param ImageType $targetType
     * @return string
     * @throws ImagickException
     */
    public function convertImage(Imagick $image, ImageType $targetType): string
    {
        $image->setImageFormat(match ($targetType) {
            ImageType::Webp => 'webp',
            ImageType::Png => 'png',
            ImageType::Jpg => 'jpeg',
            ImageType::Gif => 'gif',
            ImageType::Bmp => 'bmp',
        });

        return $image->getImageBlob();
    }

    /**
     * @param string $filename
     * @param int $widthInPx
     * @return Imagick
     * @throws ImagickException
     */
    public function changeImageSize(string $filename, int $widthInPx): Imagick
    {
        $magick = new Imagick($filename);
        if ($widthInPx > $magick->getImageWidth()) {
            return $magick;
        }

        $magick->resizeImage($widthInPx, 0, Imagick::FILTER_BOX, 1);

        return $magick;
    }
}