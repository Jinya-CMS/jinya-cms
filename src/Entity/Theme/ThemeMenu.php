<?php

namespace Jinya\Entity\Theme;

use Doctrine\ORM\Mapping as ORM;
use Jinya\Entity\Menu\Menu;

/**
 * @ORM\Entity
 * @ORM\Table(name="theme_menu")
 */
class ThemeMenu
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Theme\Theme")
     * @ORM\JoinColumn(nullable=false, name="theme_id", referencedColumnName="id")
     */
    private Theme $theme;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Menu\Menu")
     * @ORM\JoinColumn(nullable=false, name="menu_id", referencedColumnName="id")
     */
    private Menu $menu;

    /**
     * @ORM\Id
     * @ORM\Column(type="string", nullable=false)
     */
    private string $name;

    public function getTheme(): Theme
    {
        return $this->theme;
    }

    public function setTheme(Theme $theme): void
    {
        $this->theme = $theme;
    }

    public function getMenu(): Menu
    {
        return $this->menu;
    }

    public function setMenu(Menu $menu): void
    {
        $this->menu = $menu;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
