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

class MenuItemGetAllEvent extends Event
{
    public const PRE_GET_ALL = 'MenuItemPreGetAll';

    public const POST_GET_ALL = 'MenuItemPostGetAll';

    /** @var int */
    private $parentId;

    /** @var string */
    private $type;

    /** @var MenuItem[] */
    private $items;

    /**
     * MenuItemGetAllEvent constructor.
     * @param int $parentId
     * @param string $type
     * @param MenuItem[] $items
     */
    public function __construct(int $parentId, string $type, array $items)
    {
        $this->parentId = $parentId;
        $this->type = $type;
        $this->items = $items;
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
     * @return MenuItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
