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
    private $themeName;

    /** @var array */
    private $menus;

    /**
     * ThemeMenusEvent constructor.
     * @param string $themeName
     * @param array $menus
     */
    public function __construct(string $themeName, array $menus)
    {
        $this->themeName = $themeName;
        $this->menus = $menus;
    }

    /**
     * @return string
     */
    public function getThemeName(): string
    {
        return $this->themeName;
    }

    /**
     * @return array
     */
    public function getMenus(): array
    {
        return $this->menus;
    }
}
