<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 20.08.18
 * Time: 19:12
 */

namespace Jinya\Framework\Events\Artworks;

use Jinya\Entity\Gallery\ArtGallery;
use Jinya\Framework\Events\Common\CancellableEvent;

class ArtworkPositionDeleteEvent extends CancellableEvent
{
    public const PRE_DELETE = 'ArtworkPositionPreDelete';

    public const POST_DELETE = 'ArtworkPositionPostDelete';

    /** @var ArtGallery */
    private $gallery;

    /** @var int */
    private $id;

    /**
     * ArtworkPositionEvent constructor.
     * @param ArtGallery|null $gallery
     * @param int $id
     */
    public function __construct(?ArtGallery $gallery, int $id)
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
     * @return ArtGallery
     */
    public function getGallery(): ?ArtGallery
    {
        return $this->gallery;
    }
}
