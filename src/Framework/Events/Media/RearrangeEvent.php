<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 20.08.18
 * Time: 19:23
 */

namespace Jinya\Framework\Events\Media;

use Jinya\Entity\Media\Gallery;
use Jinya\Entity\Media\GalleryFilePosition;
use Jinya\Framework\Events\Common\CancellableEvent;

class RearrangeEvent extends CancellableEvent
{
    public const PRE_REARRANGE = 'GalleryFilePositionPreRearrange';

    public const POST_REARRANGE = 'GalleryFilePositionPostRearrange';

    /** @var Gallery */
    private Gallery $gallery;

    /** @var GalleryFilePosition */
    private GalleryFilePosition $galleryFilePosition;

    /** @var int */
    private int $oldPosition;

    /** @var int */
    private int $newPosition;

    /**
     * RearrangeEvent constructor.
     */
    public function __construct(
        Gallery $gallery,
        GalleryFilePosition $galleryFilePosition,
        int $oldPosition,
        int $newPosition
    ) {
        $this->gallery = $gallery;
        $this->galleryFilePosition = $galleryFilePosition;
        $this->oldPosition = $oldPosition;
        $this->newPosition = $newPosition;
    }

    public function getGallery(): Gallery
    {
        return $this->gallery;
    }

    public function getGalleryFilePosition(): GalleryFilePosition
    {
        return $this->galleryFilePosition;
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
