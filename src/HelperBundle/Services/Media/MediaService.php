<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 09.11.2017
 * Time: 19:32
 */

namespace HelperBundle\Services\Media;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use const DIRECTORY_SEPARATOR;
use function array_reverse;
use function hash_file;
use function preg_split;
use function unlink;

class MediaService implements MediaServiceInterface
{

    /** @var string */
    private $baseUrl;

    /** @var string */
    private $rootDir;

    /**
     * MediaService constructor.
     * @param string $baseUrl
     * @param string $rootDir
     */
    public function __construct($baseUrl, $rootDir)
    {
        $this->baseUrl = $baseUrl;
        $this->rootDir = $rootDir;
    }

    /**
     * @inheritdoc
     */
    public function saveMedia(UploadedFile $file, string $type): string
    {
        $savedFile = $file->move($this->rootDir . DIRECTORY_SEPARATOR . '../var/tmp');
        $savedFile = $savedFile->move($this->getFilePath(hash_file('sha256', $savedFile->getRealPath()), $type));
        $filename = $savedFile->getFilename();

        return "$this->baseUrl/public/$type/${filename}";
    }

    private function getFilePath(string $filename, string $type): string
    {
        return $this->rootDir . DIRECTORY_SEPARATOR . '../web/public/' . $type . DIRECTORY_SEPARATOR . $filename;
    }

    /**
     * Deletes the media saved under the given url
     *
     * @param string $url
     * @return void
     */
    public function deleteMedia(string $url)
    {
        $parts = preg_split('/\\//g', $url);
        $parts = array_reverse($parts);
        $filename = $parts[0];
        $type = $parts[1];
        unlink($this->getFilePath($filename, $type));
    }
}