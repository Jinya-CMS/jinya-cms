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
    private string $gallerySlug;

    /** @var int */
    private int $galleryFilePositionId;

    /** @var int */
    private int $oldPosition;

    /** @var int */
    private int $newPosition;

    /**
     * GalleryFilePositionUpdateEvent constructor.
     */
    public function __construct(string $gallerySlug, int $galleryFilePositionId, int $oldPosition, int $newPosition)
    {
        $this->gallerySlug = $gallerySlug;
        $this->galleryFilePositionId = $galleryFilePositionId;
        $this->oldPosition = $oldPosition;
        $this->newPosition = $newPosition;
    }

    public function getGallerySlug(): string
    {
        return $this->gallerySlug;
    }

    public function getGalleryFilePositionId(): int
    {
        return $this->galleryFilePositionId;
    }

    public function getOldPosition(): int
    {
        return $this->oldPosition;
    }

    public function getNewPosition(): int
    {
        return $this->newPosition;
    }
}
