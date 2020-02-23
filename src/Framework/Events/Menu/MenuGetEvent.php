<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 24.08.18
 * Time: 21:31
 */

namespace Jinya\Framework\Events\Menu;

use Jinya\Entity\Menu\Menu;
use Symfony\Contracts\EventDispatcher\Event;

class MenuGetEvent extends Event
{
    public const PRE_GET = 'MenuPreGet';

    public const POST_GET = 'MenuPostGet';

    /** @var int */
    private int $id;

    /** @var Menu|null */
    private ?Menu $menu;

    /**
     * MenuGetEvent constructor.
     * @param int $id
     * @param Menu $menu
     */
    public function __construct(int $id, ?Menu $menu)
    {
        $this->id = $id;
        $this->menu = $menu;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Menu|null
     */
    public function getMenu(): ?Menu
    {
        return $this->menu;
    }
}
