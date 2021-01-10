<?php

namespace App\Storage;

abstract class StorageBaseService
{
    public const WEB_PATH = '/jinya-content/';
    public const BASE_PATH = __ROOT__;
    public const SAVE_PATH = self::BASE_PATH . '/public/' . self::WEB_PATH;

    /**
     * @param resource $fileContent
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
