<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 24.08.18
 * Time: 21:19
 */

namespace Jinya\Framework\Events\Menu;

use Jinya\Entity\Menu\MenuItem;
use Jinya\Framework\Events\Common\CancellableEvent;

class MenuItemRemoveEvent extends CancellableEvent
{
    public const PRE_REMOVE = 'MenuItemPreRemove';

    public const POST_REMOVE = 'MenuItemPostRemove';

    private int $parentId;

    private string $type;

    private MenuItem $item;

    private int $position;

    /**
     * MenuItemGetEvent constructor.
     */
    public function __construct(int $parentId, string $type, int $position, MenuItem $item)
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

    public function getItem(): MenuItem
    {
        return $this->item;
    }

    public function getPosition(): int
    {
        return $this->position;
    }
}
