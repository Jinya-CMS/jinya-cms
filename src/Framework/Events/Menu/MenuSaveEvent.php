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

    private Menu $menu;

    /**
     * MenuSaveOrUpdateEvent constructor.
     */
    public function __construct(Menu $menu)
    {
        $this->menu = $menu;
    }

    public function getMenu(): Menu
    {
        return $this->menu;
    }
}
