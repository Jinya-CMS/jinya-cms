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

    private int $parentId;

    private string $type;

    /** @var MenuItem */
    private ?MenuItem $item;

    private int $position;

    /**
     * MenuItemGetEvent constructor.
     * @param MenuItem $item
     */
    public function __construct(int $parentId, string $type, int $position, ?MenuItem $item)
    {
        $this->parentId = $parentId;
        $this->type = $type;
        $this->item = $item;
        $this->position = $position;
    }

    public function getParentId(): int
    {
        return $this->parentId;
    }

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

    public function getPosition(): int
    {
        return $this->position;
    }
}
