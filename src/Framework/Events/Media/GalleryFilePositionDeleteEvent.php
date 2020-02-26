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

    /** @var int */
    private int $id;

    /** @var GalleryFilePosition */
    private GalleryFilePosition $galleryFilePosition;

    /**
     * GalleryFilePositionDeleteEvent constructor.
     * @param int $id
     * @param GalleryFilePosition $galleryFilePosition
     */
    public function __construct(GalleryFilePosition $galleryFilePosition, int $id)
    {
        $this->id = $id;
        $this->galleryFilePosition = $galleryFilePosition;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return GalleryFilePosition
     */
    public function getGalleryFilePosition(): GalleryFilePosition
    {
        return $this->galleryFilePosition;
    }
}
