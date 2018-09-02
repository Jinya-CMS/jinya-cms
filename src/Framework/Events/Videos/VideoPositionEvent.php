<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 20.08.18
 * Time: 19:12
 */

namespace Jinya\Framework\Events\Videos;

use Jinya\Entity\Video\VideoPosition;
use Jinya\Framework\Events\Common\CancellableEvent;

class VideoPositionEvent extends CancellableEvent
{
    public const PRE_SAVE = 'VideoPositionPreSave';

    public const POST_SAVE = 'VideoPositionPostSave';

    public const PRE_DELETE = 'VideoPositionPreDelete';

    public const POST_DELETE = 'VideoPositionPostDelete';

    public const PRE_GET = 'VideoPositionPreGet';

    public const POST_GET = 'VideoPositionPostGet';

    /** @var VideoPosition */
    private $videoPosition;

    /** @var int */
    private $id;

    /**
     * VideoPositionEvent constructor.
     * @param VideoPosition $videoPosition
     * @param int $id
     */
    public function __construct(?VideoPosition $videoPosition, int $id)
    {
        $this->videoPosition = $videoPosition;
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
     * @return VideoPosition
     */
    public function getVideoPosition(): ?VideoPosition
    {
        return $this->videoPosition;
    }
}
