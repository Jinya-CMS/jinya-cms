<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 24.08.18
 * Time: 19:02
 */

namespace Jinya\Framework\Events\Media;

use Symfony\Contracts\EventDispatcher\Event;

class MediaMoveEvent extends Event
{
    public const PRE_MOVE = 'MediaPreMove';

    public const POST_MOVE = 'MediaPostMove';

    /** @var string */
    private string $from;

    /** @var string */
    private string $type;

    /** @var string */
    private string $location;

    /**
     * MediaMoveEvent constructor.
     * @param string $from
     * @param string $type
     */
    public function __construct(string $from, string $type)
    {
        $this->from = $from;
        $this->type = $type;
        $this->location = '';
    }

    /**
     * @return string
     */
    public function getFrom(): string
    {
        return $this->from;
    }

    /**
     * @return string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getLocation(): ?string
    {
        return $this->location;
    }

    /**
     * @param string $location
     */
    public function setLocation(string $location): void
    {
        $this->location = $location;
    }
}
