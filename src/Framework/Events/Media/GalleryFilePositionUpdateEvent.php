<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 20.08.18
 * Time: 19:48
 */

namespace Jinya\Framework\Events\Media;

use Jinya\Framework\Events\Common\CancellableEvent;

class GalleryFilePositionUpdateEvent extends CancellableEvent
{
    public const PRE_UPDATE = 'GalleryFilePositionPreUpdate';

    public const POST_UPDATE = 'GalleryFilePositionPostUpdate';

    /** @var string */
    private $gallerySlug;

    /** @var int */
    private $galleryFilePositionId;

    /** @var int */
    private $oldPosition;

    /** @var int */
    private $newPosition;

    /**
     * GalleryFilePositionUpdateEvent constructor.
     * @param string $gallerySlug
     * @param int $galleryFilePositionId
     * @param int $oldPosition
     * @param int $newPosition
     */
    public function __construct(string $gallerySlug, int $galleryFilePositionId, int $oldPosition, int $newPosition)
    {
        $this->gallerySlug = $gallerySlug;
        $this->galleryFilePositionId = $galleryFilePositionId;
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
    public function getGalleryFilePositionId(): int
    {
        return $this->galleryFilePositionId;
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
