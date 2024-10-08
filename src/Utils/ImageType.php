<?php

namespace Jinya\Cms\Utils;

/**
 * Helper enum to differentiate between image file types
 */
enum ImageType
{
    /** Images of type WebP */
    case Webp;
    /** Images of type PNG */
    case Png;
    /** Images of type JPG */
    case Jpg;

    public function string(): string
    {
        return match ($this) {
            self::Webp => 'webp',
            self::Png => 'png',
            self::Jpg => 'jpg',
        };
    }
}
