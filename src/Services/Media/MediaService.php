<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 09.11.2017
 * Time: 19:32
 */

namespace Jinya\Services\Media;

use SplFileInfo;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use function array_reverse;
use function file_put_contents;
use function hash_final;
use function hash_init;
use function hash_update_file;
use function mkdir;
use function preg_split;
use function uniqid;
use function unlink;
use const DIRECTORY_SEPARATOR;

class MediaService implements MediaServiceInterface
{

    /** @var string */
    private $kernelProjectDir;

    /**
     * MediaService constructor.
     * @param string $kernelProjectDir
     */
    public function __construct(string $kernelProjectDir)
    {
        $this->kernelProjectDir = $kernelProjectDir;
    }

    /**
     * Saves the media to the storage and return the http url
     *
     * @param resource|UploadedFile $file
     * @param string $type
     * @return string
     */
    public function saveMedia($file, string $type): string
    {
        $directory = $this->getFilePath($type);
        @mkdir($directory, 775, true);

        $tmpFilename = $directory . uniqid();
        file_put_contents($tmpFilename, $file);

        $hashCtx = hash_init('sha256');
        hash_update_file($hashCtx, $tmpFilename);
        $hash = hash_final($hashCtx);

        $filename = $directory . $hash;

        $fs = new Filesystem();
        $fs->rename($tmpFilename, $filename, true);

        return "/public/$type/${hash}";
    }

    private function getFilePath(string $type): string
    {
        return $this->kernelProjectDir . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $type . DIRECTORY_SEPARATOR;
    }

    /**
     * Deletes the media saved under the given url
     *
     * @param string $url
     * @return void
     */
    public function deleteMedia(string $url)
    {
        $parts = preg_split('/\\//', $url);
        $parts = array_reverse($parts);
        $filename = $parts[0];
        $type = $parts[1];
        unlink($this->getFilePath($type) . $filename);
    }

    /**
     * Gets the media as SplFileInfo
     *
     * @param string $path
     * @return SplFileInfo
     */
    public function getMedia(string $path): SplFileInfo
    {
        return new SplFileInfo($this->kernelProjectDir . DIRECTORY_SEPARATOR . 'public' . $path);
    }
}