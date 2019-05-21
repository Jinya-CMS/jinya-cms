<?php

namespace Jinya\Services\Theme;

use Doctrine\ORM\EntityManagerInterface;
use Jinya\Entity\Artwork\Artwork;
use Jinya\Entity\Gallery\ArtGallery;
use Jinya\Entity\Gallery\VideoGallery;
use Jinya\Entity\Menu\Menu;
use Jinya\Entity\Page\Page;
use Jinya\Entity\Theme\ThemeArtGallery;
use Jinya\Entity\Theme\ThemeArtwork;
use Jinya\Entity\Theme\ThemeForm;
use Jinya\Entity\Theme\ThemeMenu;
use Jinya\Entity\Theme\ThemePage;
use Jinya\Entity\Theme\ThemeVideoGallery;
use Symfony\Component\Yaml\Yaml;

class ThemeLinkService implements ThemeLinkServiceInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var string */
    private $themeDir;

    /** @var ThemeServiceInterface */
    private $themeService;

    /**
     * ThemeLinkService constructor.
     * @param EntityManagerInterface $entityManager
     * @param string $themeDir
     * @param ThemeServiceInterface $themeService
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        string $themeDir,
        ThemeServiceInterface $themeService
    ) {
        $this->entityManager = $entityManager;
        $this->themeDir = $themeDir;
        $this->themeService = $themeService;
    }

    /**
     * Returns the link config structure for the given theme
     *
     * @param string $themeName
     * @return array
     */
    public function getLinkConfigStructure(string $themeName): array
    {
        $configFile = $this->themeDir . '/' . $themeName . '/' . ThemeService::THEME_CONFIG_YML;
        $data = Yaml::parseFile($configFile);

        if (array_key_exists('links', $data)) {
            return $data['links'];
        }

        return [];
    }

    /**
     * Links the given page with the given theme
     *
     * @param string $key
     * @param string $themeName
     * @param string $pageSlug
     */
    public function savePage(string $key, string $themeName, string $pageSlug): void
    {
        // TODO add event
        $page = $this->entityManager->find(Page::class, $pageSlug);
        $theme = $this->themeService->getThemeOrNewTheme($themeName);
        $themePage = new ThemePage();
        $themePage->setPage($page);
        $themePage->setTheme($theme);
        $themePage->setName($key);

        $theme->getPages()->add($page);

        $this->entityManager->persist($themePage);
        $this->entityManager->flush();
    }

    /**
     * Links the given form with the given theme
     *
     * @param string $key
     * @param string $themeName
     * @param string $formSlug
     */
    public function saveForm(string $key, string $themeName, string $formSlug): void
    {
        // TODO add event
        $form = $this->entityManager->find(Page::class, $formSlug);
        $theme = $this->themeService->getThemeOrNewTheme($themeName);
        $themePage = new ThemeForm();
        $themePage->setForm($form);
        $themePage->setTheme($theme);
        $themePage->setName($key);

        $theme->getForms()->add($form);

        $this->entityManager->persist($themePage);
        $this->entityManager->flush();
    }

    /**
     * Links the given menu with the given theme
     *
     * @param string $key
     * @param string $themeName
     * @param string $menuSlug
     */
    public function saveMenu(string $key, string $themeName, string $menuSlug): void
    {
        // TODO add event
        $menu = $this->entityManager->find(Menu::class, $menuSlug);
        $theme = $this->themeService->getThemeOrNewTheme($themeName);
        $themePage = new ThemeMenu();
        $themePage->setMenu($menu);
        $themePage->setTheme($theme);
        $themePage->setName($key);

        $theme->getMenus()->add($menu);

        $this->entityManager->persist($themePage);
        $this->entityManager->flush();
    }

    /**
     * Links the given artwork with the given theme
     *
     * @param string $key
     * @param string $themeName
     * @param string $artworkSlug
     */
    public function saveArtwork(string $key, string $themeName, string $artworkSlug): void
    {
        // TODO add event
        $artwork = $this->entityManager->find(Artwork::class, $artworkSlug);
        $theme = $this->themeService->getThemeOrNewTheme($themeName);
        $themePage = new ThemeArtwork();
        $themePage->setArtwork($artwork);
        $themePage->setTheme($theme);
        $themePage->setName($key);

        $theme->getArtworks()->add($artwork);

        $this->entityManager->persist($themePage);
        $this->entityManager->flush();
    }

    /**
     * Links the given art gallery with the given theme
     *
     * @param string $key
     * @param string $themeName
     * @param string $artGallerySlug
     */
    public function saveArtGallery(string $key, string $themeName, string $artGallerySlug): void
    {
        // TODO add event
        $gallery = $this->entityManager->find(ArtGallery::class, $artGallerySlug);
        $theme = $this->themeService->getThemeOrNewTheme($themeName);
        $themePage = new ThemeArtGallery();
        $themePage->setArtGallery($gallery);
        $themePage->setTheme($theme);
        $themePage->setName($key);

        $theme->getArtGalleries()->add($gallery);

        $this->entityManager->persist($themePage);
        $this->entityManager->flush();
    }

    /**
     * Links the given video gallery with the given theme
     *
     * @param string $key
     * @param string $themeName
     * @param string $videoGallerySlug
     */
    public function saveVideoGallery(string $key, string $themeName, string $videoGallerySlug): void
    {
        // TODO add event
        $gallery = $this->entityManager->find(VideoGallery::class, $videoGallerySlug);
        $theme = $this->themeService->getThemeOrNewTheme($themeName);
        $themePage = new ThemeVideoGallery();
        $themePage->setVideoGallery($gallery);
        $themePage->setTheme($theme);
        $themePage->setName($key);

        $theme->getVideoGalleries()->add($gallery);

        $this->entityManager->persist($themePage);
        $this->entityManager->flush();
    }
}
