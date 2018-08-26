<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 26.08.18
 * Time: 16:37
 */

namespace Jinya\Framework\Events\Videos;

use Jinya\Entity\Video\UploadingVideoChunk;
use Symfony\Component\EventDispatcher\Event;

class VideoUploadFinishUploadEvent extends Event
{
    public const PRE_FINISH_UPLOAD = 'VideoUploadPreFinishUpload';

    public const POST_FINISH_UPLOAD = 'VideoUploadPostFinishUpload';

    /** @var UploadingVideoChunk[] */
    private $chunks;

    /** @var string */
    private $slug;

    /** @var string */
    private $path;

    /**
     * VideoUploadFinishUploadEvent constructor.
     * @param UploadingVideoChunk[] $chunks
     * @param string $slug
     */
    public function __construct(array $chunks, string $slug)
    {
        $this->chunks = $chunks;
        $this->slug = $slug;
    }

    /**
     * @return string
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     * @return UploadingVideoChunk[]
     */
    public function getChunks(): array
    {
        return $this->chunks;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }
}