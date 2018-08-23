<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 20.08.18
 * Time: 19:48
 */

namespace Jinya\Framework\Events\Artworks;


use Jinya\Framework\Events\Common\CancellableEvent;

class ArtworkPositionUpdateEvent extends CancellableEvent
{
    public const PRE_UPDATE = 'ArtworkPositionPreUpdate';
    public const POST_UPDATE = 'ArtworkPositionPostUpdate';

    /** @var string */
    private $gallerySlug;

    /** @var integer */
    private $artworkPositionId;

    /** @var integer */
    private $oldPosition;

    /** @var integer */
    private $newPosition;

    /**
     * ArtworkPositionUpdateEvent constructor.
     * @param string $gallerySlug
     * @param int $artworkPositionId
     * @param int $oldPosition
     * @param int $newPosition
     */
    public function __construct(string $gallerySlug, int $artworkPositionId, int $oldPosition, int $newPosition)
    {
        $this->gallerySlug = $gallerySlug;
        $this->artworkPositionId = $artworkPositionId;
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
    public function getArtworkPositionId(): int
    {
        return $this->artworkPositionId;
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