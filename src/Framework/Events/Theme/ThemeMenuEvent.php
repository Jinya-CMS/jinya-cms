<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 09.12.18
 * Time: 20:46
 */

namespace Jinya\Framework\Events\Theme;

use Jinya\Framework\Events\Common\CancellableEvent;

class ThemeMenuEvent extends CancellableEvent
{
    public const PRE_SAVE = 'ThemeMenusPreSave';

    public const POST_SAVE = 'ThemeMenusPostSave';

    /** @var string */
    private string $themeName;

    /** @var array */
    private array $menus;

    /**
     * ThemeMenusEvent constructor.
     */
    public function __construct(string $themeName, array $menus)
    {
        $this->themeName = $themeName;
        $this->menus = $menus;
    }

    public function getThemeName(): string
    {
        return $this->themeName;
    }

    public function getMenus(): array
    {
        return $this->menus;
    }
}
