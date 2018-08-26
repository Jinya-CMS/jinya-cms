<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 26.08.18
 * Time: 15:11
 */

namespace Jinya\Framework\Events\Artworks;

use Jinya\Entity\Artwork\Artwork;
use Jinya\Framework\Events\Common\CancellableEvent;

class ArtworkPositionUpdateArtworkEvent extends CancellableEvent
{
    public const PRE_UPDATE_ARTWORK = 'ArtworkPositionPreUpdateArtwork';

    public const POST_UPDATE_ARTWORK = 'ArtworkPositionPostUpdateArtwork';

    /** @var Artwork */
    private $artwork;

    /** @var int */
    private $positionId;

    /**
     * ArtworkPositionUpdateArtworkEvent constructor.
     * @param Artwork $artwork
     * @param int $positionId
     */
    public function __construct(Artwork $artwork, int $positionId)
    {
        $this->artwork = $artwork;
        $this->positionId = $positionId;
    }

    /**
     * @return Artwork
     */
    public function getArtwork(): Artwork
    {
        return $this->artwork;
    }

    /**
     * @return int
     */
    public function getPositionId(): int
    {
        return $this->positionId;
    }
}