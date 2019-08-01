<?php

namespace Jinya\Entity\Theme;

use Doctrine\ORM\Mapping as ORM;
use Jinya\Entity\Page\Page;
use Jinya\Entity\SegmentPage\SegmentPage;

/**
 * @ORM\Entity
 * @ORM\Table(name="theme_segment_page")
 */
class ThemeSegmentPage
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
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\SegmentPage\SegmentPage")
     * @ORM\JoinColumn(nullable=false, name="segment_page_id", referencedColumnName="id")
     * @var SegmentPage
     */
    private $segmentPage;

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
     * @return SegmentPage
     */
    public function getSegmentPage(): SegmentPage
    {
        return $this->segmentPage;
    }

    /**
     * @param SegmentPage $segmentPage
     */
    public function setSegmentPage(SegmentPage $segmentPage): void
    {
        $this->segmentPage = $segmentPage;
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
