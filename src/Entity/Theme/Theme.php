<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 05.01.2018
 * Time: 17:09
 */

namespace Jinya\Entity\Theme;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Jinya\Entity\Menu\Menu;

/**
 * @ORM\Entity
 * @ORM\Table(name="theme")
 */
class Theme
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $previewImage;

    /**
     * @ORM\Column(type="json_array")
     * @var array
     */
    private $configuration = [];

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $description;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $displayName;

    /**
     * @var array
     * @ORM\Column(type="json")
     */
    private $scssVariables;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Jinya\Entity\Theme\ThemeMenu", mappedBy="themeId")
     */
    private $menus;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Jinya\Entity\Theme\ThemeArtGallery", mappedBy="themeId")
     */
    private $artGalleries;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Jinya\Entity\Theme\ThemeVideoGallery", mappedBy="themeId")
     */
    private $videoGalleries;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Jinya\Entity\Theme\ThemePage", mappedBy="themeId")
     */
    private $pages;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Jinya\Entity\Theme\ThemePage", mappedBy="themeId")
     */
    private $forms;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Jinya\Entity\Theme\ThemeArtwork", mappedBy="themeId")
     */
    private $artworks;

    /**
     * @var Menu
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Menu\Menu")
     * @ORM\JoinColumn(name="primary_menu_id", referencedColumnName="id", nullable=true)
     */
    private $primaryMenu;

    /**
     * @var Menu
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Menu\Menu")
     * @ORM\JoinColumn(name="secondary_menu_id", referencedColumnName="id", nullable=true)
     */
    private $secondaryMenu;

    /**
     * @var Menu
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Menu\Menu")
     * @ORM\JoinColumn(name="footer_menu_id", referencedColumnName="id", nullable=true)
     */
    private $footerMenu;

    /**
     * Theme constructor.
     */
    public function __construct()
    {
        $this->menus = new ArrayCollection();
        $this->artGalleries = new ArrayCollection();
        $this->videoGalleries = new ArrayCollection();
        $this->pages = new ArrayCollection();
        $this->forms = new ArrayCollection();
        $this->artworks = new ArrayCollection();
    }

    /**
     * @return ArrayCollection
     */
    public function getMenus(): ArrayCollection
    {
        return $this->menus;
    }

    /**
     * @param ArrayCollection $menus
     */
    public function setMenus(ArrayCollection $menus): void
    {
        $this->menus = $menus;
    }

    /**
     * @return ArrayCollection
     */
    public function getArtGalleries(): ArrayCollection
    {
        return $this->artGalleries;
    }

    /**
     * @param ArrayCollection $artGalleries
     */
    public function setArtGalleries(ArrayCollection $artGalleries): void
    {
        $this->artGalleries = $artGalleries;
    }

    /**
     * @return ArrayCollection
     */
    public function getVideoGalleries(): ArrayCollection
    {
        return $this->videoGalleries;
    }

    /**
     * @param ArrayCollection $videoGalleries
     */
    public function setVideoGalleries(ArrayCollection $videoGalleries): void
    {
        $this->videoGalleries = $videoGalleries;
    }

    /**
     * @return ArrayCollection
     */
    public function getPages(): ArrayCollection
    {
        return $this->pages;
    }

    /**
     * @param ArrayCollection $pages
     */
    public function setPages(ArrayCollection $pages): void
    {
        $this->pages = $pages;
    }

    /**
     * @return ArrayCollection
     */
    public function getForms(): ArrayCollection
    {
        return $this->forms;
    }

    /**
     * @param ArrayCollection $forms
     */
    public function setForms(ArrayCollection $forms): void
    {
        $this->forms = $forms;
    }

    /**
     * @return ArrayCollection
     */
    public function getArtworks(): ArrayCollection
    {
        return $this->artworks;
    }

    /**
     * @param ArrayCollection $artworks
     */
    public function setArtworks(ArrayCollection $artworks): void
    {
        $this->artworks = $artworks;
    }

    /**
     * @return Menu
     */
    public function getPrimaryMenu(): ?Menu
    {
        return $this->primaryMenu;
    }

    /**
     * @param Menu $primaryMenu
     */
    public function setPrimaryMenu(?Menu $primaryMenu): void
    {
        $this->primaryMenu = $primaryMenu;
    }

    /**
     * @return Menu
     */
    public function getSecondaryMenu(): ?Menu
    {
        return $this->secondaryMenu;
    }

    /**
     * @param Menu $secondaryMenu
     */
    public function setSecondaryMenu(?Menu $secondaryMenu): void
    {
        $this->secondaryMenu = $secondaryMenu;
    }

    /**
     * @return Menu
     */
    public function getFooterMenu(): ?Menu
    {
        return $this->footerMenu;
    }

    /**
     * @param Menu $footerMenu
     */
    public function setFooterMenu(?Menu $footerMenu): void
    {
        $this->footerMenu = $footerMenu;
    }

    /**
     * @return array
     */
    public function getScssVariables(): ?array
    {
        return $this->scssVariables;
    }

    /**
     * @param array $scssVariables
     */
    public function setScssVariables(array $scssVariables): void
    {
        $this->scssVariables = $scssVariables;
    }

    /**
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    /**
     * @param string $displayName
     */
    public function setDisplayName(string $displayName): void
    {
        $this->displayName = $displayName;
    }

    /**
     * @return string
     */
    public function getPreviewImage(): string
    {
        return $this->previewImage;
    }

    /**
     * @param string $previewImage
     */
    public function setPreviewImage(string $previewImage): void
    {
        $this->previewImage = $previewImage;
    }

    /**
     * @return array
     */
    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    /**
     * @param array $configuration
     */
    public function setConfiguration(array $configuration): void
    {
        $this->configuration = $configuration;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
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

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }
}
