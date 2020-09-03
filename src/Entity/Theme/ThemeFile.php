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
     */
    private Theme $theme;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Media\File")
     * @ORM\JoinColumn(nullable=false, name="file_id", referencedColumnName="id")
     */
    private File $file;

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

    public function getFile(): File
    {
        return $this->file;
    }

    public function setFile(File $file): void
    {
        $this->file = $file;
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
