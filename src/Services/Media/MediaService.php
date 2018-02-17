<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 09.11.2017
 * Time: 19:32
 */

namespace Jinya\Services\Media;

use const DIRECTORY_SEPARATOR;
use function array_reverse;
use function file_exists;
use function file_put_contents;
use function fseek;
use function hash_final;
use function hash_init;
use function hash_update_stream;
use function mkdir;
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
    public function saveMedia($file, string $type): string
    {
        $hashCtx = hash_init('sha256');
        hash_update_stream($hashCtx, $file);
        $hash = hash_final($hashCtx);
        fseek($file, 0);

        $directory = $this->getFilePath($type);
        mkdir($directory, 775, true);

        $filename = $directory . $hash;

        if (!file_exists($filename)) {
            file_put_contents($filename, $file);
        }

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
}