<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 20.08.18
 * Time: 19:48
 */

namespace Jinya\Framework\Events\Videos;

use Jinya\Framework\Events\Common\CancellableEvent;

class VideoPositionUpdateEvent extends CancellableEvent
{
    public const PRE_UPDATE = 'VideoPositionPreUpdate';

    public const POST_UPDATE = 'VideoPositionPostUpdate';

    /** @var string */
    private $gallerySlug;

    /** @var int */
    private $videoPositionId;

    /** @var int */
    private $oldPosition;

    /** @var int */
    private $newPosition;

    /**
     * VideoPositionUpdateEvent constructor.
     * @param string $gallerySlug
     * @param int $videoPositionId
     * @param int $oldPosition
     * @param int $newPosition
     */
    public function __construct(string $gallerySlug, int $videoPositionId, int $oldPosition, int $newPosition)
    {
        $this->gallerySlug = $gallerySlug;
        $this->videoPositionId = $videoPositionId;
        $this->oldPosition = $oldPosition;
        $this->newPosition = $newPosition;
    }

    /**
     * @return string
     */
    public function getGallerySlug(): string
    {
        return $this->gallerySlug;
    }

    /**
     * @return int
     */
    public function getVideoPositionId(): int
    {
        return $this->videoPositionId;
    }

    /**
     * @return int
     */
    public function getOldPosition(): int
    {
        return $this->oldPosition;
    }

    /**
     * @return int
     */
    public function getNewPosition(): int
    {
        return $this->newPosition;
    }
}
