<?php

namespace Jinya\Entity\Theme;

use Doctrine\ORM\Mapping as ORM;
use Jinya\Entity\Media\File;

/**
 * @ORM\Entity
 * @ORM\Table(name="theme_file")
 */
class ThemeFile
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Theme\Theme")
     * @ORM\JoinColumn(nullable=false, name="theme_id", referencedColumnName="id")
     * @var Theme
     */
    private $theme;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Media\File")
     * @ORM\JoinColumn(nullable=false, name="file_id", referencedColumnName="id")
     * @var File
     */
    private $file;

    /**
     * @ORM\Id
     * @ORM\Column(type="string", nullable=false)
     * @var string
     */
    private $name;

    /**
     * @return Theme
     */
    public function getTheme(): Theme
    {
        return $this->theme;
    }

    /**
     * @param Theme $theme
     */
    public function setTheme(Theme $theme): void
    {
        $this->theme = $theme;
    }

    /**
     * @return File
     */
    public function getFile(): File
    {
        return $this->file;
    }

    /**
     * @param File $file
     */
    public function setFile(File $file): void
    {
        $this->file = $file;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
