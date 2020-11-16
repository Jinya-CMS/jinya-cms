<?php

namespace Jinya\Entity\Theme;

use Doctrine\ORM\Mapping as ORM;
use Jinya\Entity\Page\Page;

/**
 * @ORM\Entity
 * @ORM\Table(name="theme_page")
 */
class ThemePage
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Theme\Theme")
     * @ORM\JoinColumn(nullable=false, name="theme_id", referencedColumnName="id")
     */
    private Theme $theme;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Page\Page")
     * @ORM\JoinColumn(nullable=false, name="page_id", referencedColumnName="id")
     */
    private Page $page;

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

    public function getPage(): Page
    {
        return $this->page;
    }

    public function setPage(Page $page): void
    {
        $this->page = $page;
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
