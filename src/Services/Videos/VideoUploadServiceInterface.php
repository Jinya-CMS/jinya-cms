<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 08.06.18
 * Time: 18:16
 */

namespace Jinya\Services\Videos;


interface VideoUploadServiceInterface
{
    /**
     * Starts the upload
     *
     * @param resource $chunk
     * @param string $slug
     */
    public function startUpload($chunk, string $slug): void;

    /**
     * Uploads a chunk
     *
     * @param resource $chunk
     * @param int $position
     * @param string $slug
     */
    public function uploadChunk($chunk, int $position, string $slug): void;

    /**
     * Finishes the upload
     *
     * @param string $slug
     * @return string
     */
    public function finishUpload(string $slug): string;
}