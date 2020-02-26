<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 20.08.18
 * Time: 09:41
 */

namespace Jinya\Framework\Events\Media;

use Jinya\Entity\Media\Gallery;
use Jinya\Framework\Events\Common\CancellableEvent;

class GalleryEvent extends CancellableEvent
{
    public const PRE_SAVE = 'GalleryPreSave';

    public const POST_SAVE = 'GalleryPostSave';

    public const PRE_GET = 'GalleryPreGet';

    public const POST_GET = 'GalleryPostGet';

    public const PRE_DELETE = 'GalleryPreDelete';

    public const POST_DELETE = 'GalleryPostDelete';

    /** @var Gallery */
    private ?Gallery $gallery;

    /** @var int */
    private ?int $id;

    /**
     * GalleryEvent constructor.
     * @param Gallery $gallery
     * @param int|null $id
     */
    public function __construct(?Gallery $gallery, ?int $id)
    {
        $this->gallery = $gallery;
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
    public function getGallery(): ?Gallery
    {
        return $this->gallery;
    }
}
