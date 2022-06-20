<?php

namespace App\Storage;

/**
 * Provides helpers for storage services. It contains several constants and a helper method for hashing files
 */
abstract class StorageBaseService
{
    /** @var string The base path for web data */
    public const WEB_PATH = '/jinya-content/';
    /** @var string The base path of Jinya CMS */
    public const BASE_PATH = __ROOT__;
    /** @var string The full base path for stored files */
    public const SAVE_PATH = self::BASE_PATH . '/public/' . self::WEB_PATH;

    /**
     * Hashes the given resources content
     *
     * @param resource $fileContent
     * @return string
     */
    protected function getFileHash($fileContent): string
    {
        rewind($fileContent);
        $hashCtx = hash_init('sha256');
        hash_update_stream($hashCtx, $fileContent);
        $result = hash_final($hashCtx);
        rewind($fileContent);

        return $result;
    }
}
