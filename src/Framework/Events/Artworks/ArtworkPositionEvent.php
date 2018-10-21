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

    public const PRE_GET = 'ArtworkPositionPreGet';

    public const POST_GET = 'ArtworkPositionPostGet';

    /** @var ArtworkPosition */
    private $artworkPosition;

    /** @var int */
    private $id;

    /**
     * ArtworkPositionEvent constructor.
     * @param ArtworkPosition $artworkPosition
     * @param int $id
     */
    public function __construct(?ArtworkPosition $artworkPosition, int $id)
    {
        $this->artworkPosition = $artworkPosition;
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
     * @return ArtworkPosition
     */
    public function getArtworkPosition(): ?ArtworkPosition
    {
        return $this->artworkPosition;
    }
}
