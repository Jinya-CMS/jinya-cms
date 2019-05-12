<?php

namespace Jinya\Services\Media;

class ConversionService implements ConversionServiceInterface
{
    /**
     * @param resource $data
     * @param int $targetType
     * @return resource
     */
    public function convertImage($data, int $targetType)
    {
        if (extension_loaded('gd') && imagetypes() & $targetType) {
            $imageString = @imagecreatefromstring($data);
            if (!empty($data)) {
                ob_start();
                switch ($targetType) {
                    case IMAGETYPE_BMP:
                        imagebmp($imageString);
                        break;
                    case IMAGETYPE_GIF:
                        imagegif($imageString);
                        break;
                    case IMAGETYPE_PNG:
                        imagepng($imageString);
                        break;
                    case IMAGETYPE_JPEG:
                        imagejpeg($imageString);
                        break;
                    case IMAGETYPE_WEBP:
                        imagewebp($imageString);
                        break;
                }
                imagedestroy($imageString);

                $image = ob_get_contents();
                ob_clean();

                $returnStream = fopen('php://temp', 'wrb+');
                fwrite($returnStream, $image);

                return $returnStream;
            }
        }

        return $data;
    }

    /**
     * @return array
     */
    public function getSupportedTypes(): array
    {
        $imageTypes = [];

        if (imagetypes() & IMAGETYPE_BMP) {
            $imageTypes[] = [IMAGETYPE_BMP => 'Bitmap'];
        }
        if (imagetypes() & IMAGETYPE_JPEG) {
            $imageTypes[] = [IMAGETYPE_JPEG => 'JPEG'];
        }
        if (imagetypes() & IMAGETYPE_PNG) {
            $imageTypes[] = [IMAGETYPE_PNG => 'PNG'];
        }
        if (imagetypes() & IMAGETYPE_GIF) {
            $imageTypes[] = [IMAGETYPE_GIF => 'Gif'];
        }
        if (imagetypes() & IMAGETYPE_WEBP) {
            $imageTypes[] = [IMAGETYPE_WEBP => 'WebP'];
        }

        return $imageTypes;
    }
}