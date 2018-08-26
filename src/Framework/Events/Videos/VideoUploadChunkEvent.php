<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 26.08.18
 * Time: 16:33
 */

namespace Jinya\Framework\Events\Videos;

use Jinya\Framework\Events\Common\CancellableEvent;

class VideoUploadChunkEvent extends CancellableEvent
{
    public const PRE_UPLOAD_CHUNK = 'VideoUploadPreUploadChunk';

    public const POST_UPLOAD_CHUNK = 'VideoUploadPostUploadChunk';

    /** @var resource */
    private $chunk;

    /** @var int */
    private $position;

    /** @var string */
    private $slug;

    /**
     * VideoUploadChunkEvent constructor.
     * @param resource $chunk
     * @param int $position
     * @param string $slug
     */
    public function __construct($chunk, int $position, string $slug)
    {
        $this->chunk = $chunk;
        $this->position = $position;
        $this->slug = $slug;
    }

    /**
     * @return resource
     */
    public function getChunk()
    {
        return $this->chunk;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }
}
