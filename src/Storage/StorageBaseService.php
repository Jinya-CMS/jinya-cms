<?php

namespace App\Storage;

abstract class StorageBaseService
{
    public const WEB_PATH = '/jinya-content/';
    public const BASE_PATH = __ROOT__;
    public const SAVE_PATH = self::BASE_PATH . '/public/jinya-content/' . self::WEB_PATH;

    /**
     * @param resource $fileContent
     * @return string
     */
    protected function getFileHash($fileContent): string
    {
        $hashCtx = hash_init('sha256');
        hash_update_stream($hashCtx, $fileContent);
        return hash_final($hashCtx);
    }
}