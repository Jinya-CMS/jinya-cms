<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 08.06.18
 * Time: 18:50
 */

namespace Jinya\Services\Videos;


use Jinya\Services\Media\MediaServiceInterface;

class VideoUploadService implements VideoUploadServiceInterface
{
    /** @var VideoServiceInterface */
    private $videoService;
    /** @var MediaServiceInterface */
    private $mediaService;
    /** @var string */
    private $tmpDir;

    /**
     * VideoUploadService constructor.
     * @param VideoServiceInterface $videoService
     * @param MediaServiceInterface $mediaService
     * @param string $tmpDir
     */
    public function __construct(VideoServiceInterface $videoService, MediaServiceInterface $mediaService, string $tmpDir)
    {
        $this->videoService = $videoService;
        $this->mediaService = $mediaService;
        $this->tmpDir = $tmpDir;
    }

    /**
     * Starts the upload
     *
     * @param resource $chunk
     * @param string $slug
     */
    public function startUpload($chunk, string $slug): void
    {
        // TODO: Implement startUpload() method.
    }

    /**
     * Uploads a chunk
     *
     * @param resource $chunk
     * @param int $position
     * @param string $slug
     */
    public function uploadChunk($chunk, int $position, string $slug): void
    {
        // TODO: Implement uploadChunk() method.
    }

    /**
     * Finishes the upload
     *
     * @param string $slug
     * @return string
     */
    public function finishUpload(string $slug): string
    {
        // TODO: Implement finishUpload() method.
    }
}