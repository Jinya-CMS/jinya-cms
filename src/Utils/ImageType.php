<?php

namespace App\Utils;

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
    /**
     * Images of type GIF
     * @deprecated GIF is a rather old format and you should use either PNG or WebP to replace it. Both alternatives support alpha channel
     */
    case Gif;
    /**
     * Images of type BMP
     * @deprecated BMP is a horrible format for web due to the huge size of images best use JPG or WebP instead
     */
    case Bmp;
}