<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 20.08.18
 * Time: 19:12
 */

namespace Jinya\Framework\Events\Media;

use Jinya\Entity\Media\GalleryFilePosition;
use Jinya\Framework\Events\Common\CancellableEvent;

class GalleryFilePositionDeleteEvent extends CancellableEvent
{
    public const PRE_DELETE = 'GalleryFilePositionPreDelete';

    public const POST_DELETE = 'GalleryFilePositionPostDelete';

    private int $id;

    private GalleryFilePosition $galleryFilePosition;

    /**
     * GalleryFilePositionDeleteEvent constructor.
     */
    public function __construct(GalleryFilePosition $galleryFilePosition, int $id)
    {
        $this->id = $id;
        $this->galleryFilePosition = $galleryFilePosition;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getGalleryFilePosition(): GalleryFilePosition
    {
        return $this->galleryFilePosition;
    }
}
