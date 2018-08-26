<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 26.08.18
 * Time: 16:41
 */

namespace Jinya\Framework\Events\Videos;

use Jinya\Entity\Video\UploadingVideoChunk;
use Jinya\Framework\Events\Common\CancellableEvent;

class VideoUploadCleanupAfterUploadEvent extends CancellableEvent
{
    public const PRE_CLEANUP_AFTER_UPLOAD = 'VideoUploadPreCleanupAfterUpload';

    public const POST_CLEANUP_AFTER_UPLOAD = 'VideoUploadPostCleanupAfterUpload';

    /** @var string */
    private $slug;

    /** @var UploadingVideoChunk[] */
    private $chunks;

    /**
     * VideoUploadCleanupAfterUploadEvent constructor.
     * @param string $slug
     * @param UploadingVideoChunk[] $chunks
     */
    public function __construct(string $slug, array $chunks)
    {
        $this->slug = $slug;
        $this->chunks = $chunks;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @return UploadingVideoChunk[]
     */
    public function getChunks(): array
    {
        return $this->chunks;
    }
}
