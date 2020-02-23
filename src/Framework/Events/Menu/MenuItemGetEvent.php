<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 24.08.18
 * Time: 19:21
 */

namespace Jinya\Framework\Events\Menu;

use Jinya\Entity\Menu\MenuItem;
use Symfony\Contracts\EventDispatcher\Event;

class MenuItemGetEvent extends Event
{
    public const PRE_GET = 'MenuItemPreGet';

    public const POST_GET = 'MenuItemPostGet';

    /** @var int */
    private int $parentId;

    /** @var string */
    private string $type;

    /** @var MenuItem */
    private ?MenuItem $item;

    /** @var int */
    private int $position;

    /**
     * MenuItemGetEvent constructor.
     * @param int $parentId
     * @param string $type
     * @param int $position
     * @param MenuItem $item
     */
    public function __construct(int $parentId, string $type, int $position, ?MenuItem $item)
    {
        $this->parentId = $parentId;
        $this->type = $type;
        $this->item = $item;
        $this->position = $position;
    }

    /**
     * @return int
     */
    public function getParentId(): int
    {
        return $this->parentId;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return MenuItem
     */
    public function getItem(): ?MenuItem
    {
        return $this->item;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }
}
