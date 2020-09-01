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
     */
    private int $id;

    /**
     * @ORM\Column(type="string")
     */
    private string $previewImage;

    /**
     * @ORM\Column(type="json")
     */
    private array $configuration = [];

    /**
     * @ORM\Column(type="string")
     */
    private string $description;

    /**
     * @ORM\Column(type="string")
     */
    private string $name;

    /**
     * @ORM\Column(type="string")
     */
    private string $displayName;

    /**
     * @ORM\Column(type="json")
     */
    private array $scssVariables;

    /**
     * @ORM\OneToMany(targetEntity="Jinya\Entity\Theme\ThemeMenu", mappedBy="theme")
     */
    private Collection $menus;

    /**
     * @ORM\OneToMany(targetEntity="Jinya\Entity\Theme\ThemePage", mappedBy="theme")
     */
    private Collection $pages;

    /**
     * @ORM\OneToMany(targetEntity="Jinya\Entity\Theme\ThemeGallery", mappedBy="theme")
     */
    private Collection $galleries;

    /**
     * @ORM\OneToMany(targetEntity="Jinya\Entity\Theme\ThemeFile", mappedBy="theme")
     */
    private Collection $files;

    /**
     * @ORM\OneToMany(targetEntity="Jinya\Entity\Theme\ThemeSegmentPage", mappedBy="theme")
     */
    private Collection $segmentPages;

    /**
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

    public function getGalleries(): Collection
    {
        return $this->galleries;
    }

    public function setGalleries(Collection $galleries): void
    {
        $this->galleries = $galleries;
    }

    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function setFiles(Collection $files): void
    {
        $this->files = $files;
    }

    public function getMenus(): Collection
    {
        return $this->menus;
    }

    public function setMenus(Collection $menus): void
    {
        $this->menus = $menus;
    }

    public function getPages(): Collection
    {
        return $this->pages;
    }

    public function setPages(Collection $pages): void
    {
        $this->pages = $pages;
    }

    public function getForms(): Collection
    {
        return $this->forms;
    }

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

    public function setScssVariables(array $scssVariables): void
    {
        $this->scssVariables = $scssVariables;
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    public function setDisplayName(string $displayName): void
    {
        $this->displayName = $displayName;
    }

    public function getPreviewImage(): string
    {
        return $this->previewImage;
    }

    public function setPreviewImage(string $previewImage): void
    {
        $this->previewImage = $previewImage;
    }

    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    public function setConfiguration(array $configuration): void
    {
        $this->configuration = $configuration;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getSegmentPages(): Collection
    {
        return $this->segmentPages;
    }

    public function setSegmentPages(Collection $segmentPages): void
    {
        $this->segmentPages = $segmentPages;
    }
}
