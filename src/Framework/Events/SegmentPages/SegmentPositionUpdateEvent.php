<?php

namespace Jinya\Framework\Events\SegmentPages;

use Jinya\Framework\Events\Common\CancellableEvent;

class SegmentPositionUpdateEvent extends CancellableEvent
{
    public const PRE_UPDATE = 'SegmentPositionPreUpdate';

    public const POST_UPDATE = 'SegmentPositionPostUpdate';

    /** @var string */
    private string $segmentPageSlug;

    /** @var int */
    private int $segmentId;

    /** @var int */
    private int $oldPosition;

    /** @var int */
    private int $newPosition;

    /**
     * SegmentPositionUpdateEvent constructor.
     * @param string $segmentPageSlug
     * @param int $segmentId
     * @param int $oldPosition
     * @param int $newPosition
     */
    public function __construct(string $segmentPageSlug, int $segmentId, int $oldPosition, int $newPosition)
    {
        $this->segmentPageSlug = $segmentPageSlug;
        $this->segmentId = $segmentId;
        $this->oldPosition = $oldPosition;
        $this->newPosition = $newPosition;
    }

    /**
     * @return string
     */
    public function getSegmentPageSlug(): string
    {
        return $this->segmentPageSlug;
    }

    /**
     * @return int
     */
    public function getSegmentId(): int
    {
        return $this->segmentId;
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
