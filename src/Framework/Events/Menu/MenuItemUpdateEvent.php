<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 24.08.18
 * Time: 21:25
 */

namespace Jinya\Framework\Events\Menu;

use Jinya\Entity\Menu\MenuItem;
use Jinya\Framework\Events\Common\CancellableEvent;

class MenuItemUpdateEvent extends CancellableEvent
{
    public const PRE_UPDATE = 'MenuItemPreUpdate';

    public const POST_UPDATE = 'MenuItemPostUpdate';

    /** @var MenuItem */
    private MenuItem $item;

    /**
     * MenuItemUpdateEvent constructor.
     * @param MenuItem $item
     */
    public function __construct(MenuItem $item)
    {
        $this->item = $item;
    }

    /**
     * @return MenuItem
     */
    public function getItem(): MenuItem
    {
        return $this->item;
    }
}
