<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 20.08.18
 * Time: 09:41
 */

namespace Jinya\Framework\Events\SegmentPages;

use Jinya\Entity\SegmentPage\Segment;
use Jinya\Framework\Events\Common\CancellableEvent;

class SegmentEvent extends CancellableEvent
{
    public const PRE_SAVE = 'SegmentPreSave';

    public const POST_SAVE = 'SegmentPostSave';

    public const PRE_GET = 'SegmentPreGet';

    public const POST_GET = 'SegmentPostGet';

    public const PRE_DELETE = 'SegmentPreDelete';

    public const POST_DELETE = 'SegmentPostDelete';

    /** @var Segment|null */
    private $segment;

    /** @var int */
    private $id;

    /**
     * SegmentEvent constructor.
     * @param Segment|null $segment
     * @param int $id
     */
    public function __construct(?Segment $segment, int $id)
    {
        $this->segment = $segment;
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
     * @return Segment
     */
    public function getSegment(): ?Segment
    {
        return $this->segment;
    }
}
