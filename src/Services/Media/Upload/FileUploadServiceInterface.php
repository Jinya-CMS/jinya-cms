<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 08.06.18
 * Time: 18:16
 */

namespace Jinya\Services\Media\Upload;

interface FileUploadServiceInterface
{
    /**
     * Starts the upload
     */
    public function startUpload(int $id): void;

    /**
     * Uploads a chunk
     *
     * @param resource $chunk
     */
    public function uploadChunk($chunk, int $position, int $id): void;

    /**
     * Finishes the upload
     */
    public function finishUpload(int $id): string;

    /**
     * Removes all chunk data after upload
     */
    public function cleanupAfterUpload(int $id): void;
}
