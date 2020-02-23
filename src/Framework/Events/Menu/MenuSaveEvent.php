<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 24.08.18
 * Time: 21:45
 */

namespace Jinya\Framework\Events\Menu;

use Jinya\Entity\Menu\Menu;
use Jinya\Framework\Events\Common\CancellableEvent;

class MenuSaveEvent extends CancellableEvent
{
    public const PRE_SAVE = 'MenuPreSave';

    public const POST_SAVE = 'MenuPostSave';

    /** @var Menu */
    private Menu $menu;

    /**
     * MenuSaveOrUpdateEvent constructor.
     * @param Menu $menu
     */
    public function __construct(Menu $menu)
    {
        $this->menu = $menu;
    }

    /**
     * @return Menu
     */
    public function getMenu(): Menu
    {
        return $this->menu;
    }
}
