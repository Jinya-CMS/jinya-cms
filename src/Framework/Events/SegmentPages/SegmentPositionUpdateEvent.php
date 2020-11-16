<?php

namespace Jinya\Framework\Events\SegmentPages;

use Jinya\Framework\Events\Common\CancellableEvent;

class SegmentPositionUpdateEvent extends CancellableEvent
{
    public const PRE_UPDATE = 'SegmentPositionPreUpdate';

    public const POST_UPDATE = 'SegmentPositionPostUpdate';

    private string $segmentPageSlug;

    private int $segmentId;

    private int $oldPosition;

    private int $newPosition;

    /**
     * SegmentPositionUpdateEvent constructor.
     */
    public function __construct(string $segmentPageSlug, int $segmentId, int $oldPosition, int $newPosition)
    {
        $this->segmentPageSlug = $segmentPageSlug;
        $this->segmentId = $segmentId;
        $this->oldPosition = $oldPosition;
        $this->newPosition = $newPosition;
    }

    public function getSegmentPageSlug(): string
    {
        return $this->segmentPageSlug;
    }

    public function getSegmentId(): int
    {
        return $this->segmentId;
    }

    public function getOldPosition(): int
    {
        return $this->oldPosition;
    }

    public function getNewPosition(): int
    {
        return $this->newPosition;
    }
}
