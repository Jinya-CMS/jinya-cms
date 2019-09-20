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
     *
     * @param int $id
     */
    public function startUpload(int $id): void;

    /**
     * Uploads a chunk
     *
     * @param resource $chunk
     * @param int $position
     * @param int $id
     */
    public function uploadChunk($chunk, int $position, int $id): void;

    /**
     * Finishes the upload
     *
     * @param int $id
     * @return string
     */
    public function finishUpload(int $id): string;

    /**
     * Removes all chunk data after upload
     *
     * @param int $id
     */
    public function cleanupAfterUpload(int $id): void;
}
