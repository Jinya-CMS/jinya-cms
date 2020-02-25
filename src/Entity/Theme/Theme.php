<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 05.01.2018
 * Time: 17:09
 */

namespace Jinya\Entity\Theme;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
    private int $id;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private string $previewImage;

    /**
     * @ORM\Column(type="json")
     * @var array
     */
    private array $configuration = [];

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private string $description;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private string $name;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private string $displayName;

    /**
     * @var array
     * @ORM\Column(type="json")
     */
    private array $scssVariables;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="Jinya\Entity\Theme\ThemeMenu", mappedBy="theme")
     */
    private Collection $menus;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="Jinya\Entity\Theme\ThemePage", mappedBy="theme")
     */
    private Collection $pages;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="Jinya\Entity\Theme\ThemeGallery", mappedBy="theme")
     */
    private Collection $galleries;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="Jinya\Entity\Theme\ThemeFile", mappedBy="theme")
     */
    private Collection $files;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="Jinya\Entity\Theme\ThemeSegmentPage", mappedBy="theme")
     */
    private Collection $segmentPages;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="Jinya\Entity\Theme\ThemeForm", mappedBy="theme", fetch="EAGER")
     */
    private Collection $forms;

    /**
     * @var Menu
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Menu\Menu")
     * @ORM\JoinColumn(name="primary_menu_id", referencedColumnName="id", nullable=true)
     */
    private ?Menu $primaryMenu;

    /**
     * @var Menu
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Menu\Menu")
     * @ORM\JoinColumn(name="secondary_menu_id", referencedColumnName="id", nullable=true)
     */
    private ?Menu $secondaryMenu;

    /**
     * @var Menu
     * @ORM\ManyToOne(targetEntity="Jinya\Entity\Menu\Menu")
     * @ORM\JoinColumn(name="footer_menu_id", referencedColumnName="id", nullable=true)
     */
    private ?Menu $footerMenu;

    /**
     * Theme constructor.
     */
    public function __construct()
    {
        $this->menus = new ArrayCollection();
        $this->pages = new ArrayCollection();
        $this->forms = new ArrayCollection();
        $this->segmentPages = new ArrayCollection();
        $this->galleries = new ArrayCollection();
        $this->files = new ArrayCollection();
    }

    /**
     * @return Collection
     */
    public function getGalleries(): Collection
    {
        return $this->galleries;
    }

    /**
     * @param Collection $galleries
     */
    public function setGalleries(Collection $galleries): void
    {
        $this->galleries = $galleries;
    }

    /**
     * @return Collection
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    /**
     * @param Collection $files
     */
    public function setFiles(Collection $files): void
    {
        $this->files = $files;
    }

    /**
     * @return Collection
     */
    public function getMenus(): Collection
    {
        return $this->menus;
    }

    /**
     * @param Collection $menus
     */
    public function setMenus(Collection $menus): void
    {
        $this->menus = $menus;
    }

    /**
     * @return Collection
     */
    public function getPages(): Collection
    {
        return $this->pages;
    }

    /**
     * @param Collection $pages
     */
    public function setPages(Collection $pages): void
    {
        $this->pages = $pages;
    }

    /**
     * @return Collection
     */
    public function getForms(): Collection
    {
        return $this->forms;
    }

    /**
     * @param Collection $forms
     */
    public function setForms(Collection $forms): void
    {
        $this->forms = $forms;
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

    /**
     * @return Collection
     */
    public function getSegmentPages(): Collection
    {
        return $this->segmentPages;
    }

    /**
     * @param Collection $segmentPages
     */
    public function setSegmentPages(Collection $segmentPages): void
    {
        $this->segmentPages = $segmentPages;
    }
}
