<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 20.08.18
 * Time: 19:12
 */

namespace Jinya\Framework\Events\Artworks;


use Jinya\Entity\Artwork\ArtworkPosition;
use Jinya\Framework\Events\Common\CancellableEvent;

class ArtworkPositionEvent extends CancellableEvent
{
    public const PRE_SAVE = 'ArtworkPositionPreSave';
    public const POST_SAVE = 'ArtworkPositionPostSave';

    /** @var ArtworkPosition */
    private $artworkPosition;

    /**
     * ArtworkPositionEvent constructor.
     * @param ArtworkPosition $artworkPosition
     */
    public function __construct(ArtworkPosition $artworkPosition)
    {
        $this->artworkPosition = $artworkPosition;
    }

    /**
     * @return ArtworkPosition
     */
    public function getArtworkPosition(): ArtworkPosition
    {
        return $this->artworkPosition;
    }
}