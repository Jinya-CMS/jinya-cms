<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 09.11.2017
 * Time: 19:32
 */

namespace Jinya\Services\Media;

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
    private $kernelProjectDir;

    /**
     * MediaService constructor.
     * @param string $baseUrl
     * @param string $kernelProjectDir
     */
    public function __construct(string $baseUrl, string $kernelProjectDir)
    {
        $this->baseUrl = $baseUrl;
        $this->kernelProjectDir = $kernelProjectDir;
    }

    /**
     * @inheritdoc
     */
    public function saveMedia(UploadedFile $file, string $type): string
    {
        $savedFile = $file->move($this->kernelProjectDir . DIRECTORY_SEPARATOR . 'var/tmp');
        $savedFile = $savedFile->move($this->getFilePath($type), hash_file('sha256', $savedFile->getRealPath()));
        $filename = $savedFile->getFilename();

        return "$this->baseUrl/public/$type/${filename}";
    }

    private function getFilePath(string $type): string
    {
        return $this->kernelProjectDir . DIRECTORY_SEPARATOR . 'public/public/' . $type . DIRECTORY_SEPARATOR;
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
}