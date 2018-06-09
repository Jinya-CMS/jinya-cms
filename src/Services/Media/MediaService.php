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

class MediaService implements MediaServiceInterface
{
    /** @var string */
    private $kernelProjectDir;

    /** @var string */
    private $tmpDir;

    /**
     * MediaService constructor.
     * @param string $kernelProjectDir
     * @param string $tmpDir
     */
    public function __construct(string $kernelProjectDir, string $tmpDir)
    {
        $this->kernelProjectDir = $kernelProjectDir;
        $this->tmpDir = $tmpDir;
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
        $tmpFilename = $this->tmpDir . DIRECTORY_SEPARATOR . uniqid();
        file_put_contents($tmpFilename, $file);

        return $this->moveFile($tmpFilename, $type);
    }

    /**
     * @param string $type
     * @param string $oldFile
     * @return string
     */
    private function moveFile(string $oldFile, string $type): string
    {
        $directory = $this->getFilePath($type);
        @mkdir($directory, 0775, true);

        $hashCtx = hash_init('sha256');
        hash_update_file($hashCtx, $oldFile);
        $hash = hash_final($hashCtx);

        $filename = $directory . $hash;

        $fs = new Filesystem();
        $fs->rename($oldFile, $filename, true);

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

    /**
     * Moves a file from the given path to the correct media path
     *
     * @param string $from
     * @param string $type
     * @return string
     */
    public function moveMedia(string $from, string $type): string
    {
        return $this->moveFile($from, $type);
    }
}
