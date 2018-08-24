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
    private $parentId;

    /** @var string */
    private $type;

    /** @var MenuItem */
    private $item;

    /** @var int */
    private $position;

    /**
     * MenuItemGetEvent constructor.
     * @param int $parentId
     * @param string $type
     * @param int $position
     * @param MenuItem $item
     */
    public function __construct(int $parentId, string $type, int $position, MenuItem $item)
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
    public function getItem(): MenuItem
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
