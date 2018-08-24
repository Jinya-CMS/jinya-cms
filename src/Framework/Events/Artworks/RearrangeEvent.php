<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 20.08.18
 * Time: 19:23
 */

namespace Jinya\Framework\Events\Artworks;

use Jinya\Entity\Artwork\ArtworkPosition;
use Jinya\Entity\Gallery\ArtGallery;
use Jinya\Framework\Events\Common\CancellableEvent;

class RearrangeEvent extends CancellableEvent
{
    public const PRE_REARRANGE = 'ArtworkPositionPreRearrange';

    public const POST_REARRANGE = 'ArtworkPositionPostRearrange';

    /** @var ArtGallery */
    private $gallery;

    /** @var ArtworkPosition */
    private $artworkPosition;

    /** @var int */
    private $oldPosition;

    /** @var int */
    private $newPosition;

    /**
     * RearrangeEvent constructor.
     * @param ArtGallery $gallery
     * @param ArtworkPosition $artworkPosition
     * @param int $oldPosition
     * @param int $newPosition
     */
    public function __construct(ArtGallery $gallery, ArtworkPosition $artworkPosition, int $oldPosition, int $newPosition)
    {
        $this->gallery = $gallery;
        $this->artworkPosition = $artworkPosition;
        $this->oldPosition = $oldPosition;
        $this->newPosition = $newPosition;
    }

    /**
     * @return ArtGallery
     */
    public function getGallery(): ArtGallery
    {
        return $this->gallery;
    }

    /**
     * @return ArtworkPosition
     */
    public function getArtworkPosition(): ArtworkPosition
    {
        return $this->artworkPosition;
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
