<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 20.08.18
 * Time: 09:41
 */

namespace Jinya\Framework\Events\Media;

use Jinya\Entity\Media\Gallery;
use Jinya\Entity\Media\GalleryFilePosition;
use Jinya\Framework\Events\Common\CancellableEvent;

class GalleryFilePositionEvent extends CancellableEvent
{
    public const PRE_SAVE = 'GalleryFilePositionPreSave';

    public const POST_SAVE = 'GalleryFilePositionPostSave';

    public const PRE_GET = 'GalleryFilePositionPreGet';

    public const POST_GET = 'GalleryFilePositionPostGet';

    public const PRE_DELETE = 'GalleryFilePositionPreDelete';

    public const POST_DELETE = 'GalleryFilePositionPostDelete';

    /** @var GalleryFilePosition */
    private $galleryFilePosition;

    /** @var int */
    private $id;

    /**
     * GalleryEvent constructor.
     * @param GalleryFilePosition $galleryFilePosition
     * @param int|null $id
     */
    public function __construct(?GalleryFilePosition $galleryFilePosition, ?int $id)
    {
        $this->galleryFilePosition = $galleryFilePosition;
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Gallery
     */
    public function getGalleryFilePosition(): ?GalleryFilePosition
    {
        return $this->galleryFilePosition;
    }
}
