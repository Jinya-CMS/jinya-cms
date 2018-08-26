<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 26.08.18
 * Time: 15:11
 */

namespace Jinya\Framework\Events\Videos;

use Jinya\Framework\Events\Common\CancellableEvent;

class VideoPositionUpdateVideoEvent extends CancellableEvent
{
    public const PRE_UPDATE_VIDEO = 'VideoPositionPreUpdateVideo';

    public const POST_UPDATE_VIDEO = 'VideoPositionPostUpdateVideo';

    /** @var string */
    private $videoSlug;

    /** @var string */
    private $type;

    /** @var int */
    private $positionId;

    /**
     * VideoPositionUpdateVideoEvent constructor.
     * @param string $videoSlug
     * @param string $type
     * @param int $positionId
     */
    public function __construct(string $videoSlug, string $type, int $positionId)
    {
        $this->videoSlug = $videoSlug;
        $this->type = $type;
        $this->positionId = $positionId;
    }

    /**
     * @return string
     */
    public function getVideoSlug(): string
    {
        return $this->videoSlug;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getPositionId(): int
    {
        return $this->positionId;
    }
}