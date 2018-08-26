<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 20.08.18
 * Time: 19:23
 */

namespace Jinya\Framework\Events\Videos;

use Jinya\Entity\Gallery\VideoGallery;
use Jinya\Entity\Video\VideoPosition;
use Jinya\Framework\Events\Common\CancellableEvent;

class RearrangeEvent extends CancellableEvent
{
    public const PRE_REARRANGE = 'VideoPositionPreRearrange';

    public const POST_REARRANGE = 'VideoPositionPostRearrange';

    /** @var VideoGallery */
    private $gallery;

    /** @var VideoPosition */
    private $videoPosition;

    /** @var int */
    private $oldPosition;

    /** @var int */
    private $newPosition;

    /**
     * RearrangeEvent constructor.
     * @param VideoGallery $gallery
     * @param VideoPosition $videoPosition
     * @param int $oldPosition
     * @param int $newPosition
     */
    public function __construct(VideoGallery $gallery, VideoPosition $videoPosition, int $oldPosition, int $newPosition)
    {
        $this->gallery = $gallery;
        $this->videoPosition = $videoPosition;
        $this->oldPosition = $oldPosition;
        $this->newPosition = $newPosition;
    }

    /**
     * @return VideoGallery
     */
    public function getGallery(): VideoGallery
    {
        return $this->gallery;
    }

    /**
     * @return VideoPosition
     */
    public function getVideoPosition(): VideoPosition
    {
        return $this->videoPosition;
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
