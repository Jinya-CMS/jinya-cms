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

class MenuItemAddEvent extends CancellableEvent
{
    public const PRE_ADD = 'MenuItemPreAdd';

    public const POST_ADD = 'MenuItemPostAdd';

    /** @var int */
    private int $parentId;

    /** @var string */
    private string $type;

    /** @var MenuItem */
    private MenuItem $item;

    /** @var int */
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
