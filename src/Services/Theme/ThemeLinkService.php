<?php

namespace Jinya\Services\Theme;

use Doctrine\ORM\EntityManagerInterface;
use Jinya\Entity\Theme\ThemeArtGallery;
use Jinya\Entity\Theme\ThemeArtwork;
use Jinya\Entity\Theme\ThemeForm;
use Jinya\Entity\Theme\ThemeMenu;
use Jinya\Entity\Theme\ThemePage;
use Jinya\Entity\Theme\ThemeSegmentPage;
use Jinya\Entity\Theme\ThemeVideoGallery;
use Jinya\Framework\Events\Theme\ThemeLinkEvent;
use Jinya\Services\Artworks\ArtworkServiceInterface;
use Jinya\Services\Form\FormServiceInterface;
use Jinya\Services\Galleries\ArtGalleryServiceInterface;
use Jinya\Services\Galleries\VideoGalleryServiceInterface;
use Jinya\Services\Menu\MenuServiceInterface;
use Jinya\Services\Pages\PageServiceInterface;
use Jinya\Services\SegmentPages\SegmentPageServiceInterface;
use Symfony\Component\Yaml\Yaml;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ThemeLinkService implements ThemeLinkServiceInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var string */
    private $themeDir;

    /** @var ThemeServiceInterface */
    private $themeService;

    /** @var VideoGalleryServiceInterface */
    private $videoGalleryService;

    /** @var PageServiceInterface */
    private $pageService;

    /** @var SegmentPageServiceInterface */
    private $segmentPageService;

    /** @var FormServiceInterface */
    private $formService;

    /** @var MenuServiceInterface */
    private $menuService;

    /** @var ArtGalleryServiceInterface */
    private $artGalleryService;

    /** @var ArtworkServiceInterface */
    private $artworkService;

    /**
     * ThemeLinkService constructor.
     * @param EntityManagerInterface $entityManager
     * @param EventDispatcherInterface $eventDispatcher
     * @param string $themeDir
     * @param ThemeServiceInterface $themeService
     * @param VideoGalleryServiceInterface $videoGalleryService
     * @param PageServiceInterface $pageService
     * @param SegmentPageServiceInterface $segmentPageService
     * @param FormServiceInterface $formService
     * @param MenuServiceInterface $menuService
     * @param ArtGalleryServiceInterface $artGalleryService
     * @param ArtworkServiceInterface $artworkService
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher,
        string $themeDir,
        ThemeServiceInterface $themeService,
        VideoGalleryServiceInterface $videoGalleryService,
        PageServiceInterface $pageService,
        SegmentPageServiceInterface $segmentPageService,
        FormServiceInterface $formService,
        MenuServiceInterface $menuService,
        ArtGalleryServiceInterface $artGalleryService,
        ArtworkServiceInterface $artworkService
    ) {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->themeDir = $themeDir;
        $this->themeService = $themeService;
        $this->videoGalleryService = $videoGalleryService;
        $this->pageService = $pageService;
        $this->segmentPageService = $segmentPageService;
        $this->formService = $formService;
        $this->menuService = $menuService;
        $this->artGalleryService = $artGalleryService;
        $this->artworkService = $artworkService;
    }

    /**
     * Returns the link config structure for the given theme
     *
     * @param string $themeName
     * @return array
     */
    public function getLinkConfigStructure(string $themeName): array
    {
        $configFile = implode(DIRECTORY_SEPARATOR, [$this->themeDir, $themeName, ThemeService::THEME_CONFIG_YML]);
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
        $this->eventDispatcher->dispatch(
            new ThemeLinkEvent($themeName, $key, 'page', $pageSlug),
            ThemeLinkEvent::PRE_SAVE
        );
        $page = $this->pageService->get($pageSlug);
        $theme = $this->themeService->getThemeOrNewTheme($themeName);
        $themePage = new ThemePage();
        $themePage->setPage($page);
        $themePage->setTheme($theme);
        $themePage->setName($key);

        $theme->getPages()->add($themePage);

        $this->entityManager->persist($themePage);
        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(
            new ThemeLinkEvent($themeName, $key, 'page', $pageSlug),
            ThemeLinkEvent::POST_SAVE
        );
    }

    /**
     * Links the given segment page with the given theme
     *
     * @param string $key
     * @param string $themeName
     * @param string $segmentPageSlug
     */
    public function saveSegmentPage(string $key, string $themeName, string $segmentPageSlug): void
    {
        $this->eventDispatcher->dispatch(
            new ThemeLinkEvent($themeName, $key, 'segment_page', $segmentPageSlug),
            ThemeLinkEvent::PRE_SAVE
        );
        $segmentPage = $this->segmentPageService->get($segmentPageSlug);
        $theme = $this->themeService->getThemeOrNewTheme($themeName);
        $themeSegmentPage = new ThemeSegmentPage();
        $themeSegmentPage->setSegmentPage($segmentPage);
        $themeSegmentPage->setTheme($theme);
        $themeSegmentPage->setName($key);

        $theme->getSegmentPages()->add($themeSegmentPage);

        $this->entityManager->persist($themeSegmentPage);
        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(
            new ThemeLinkEvent($themeName, $key, 'segment_page', $segmentPageSlug),
            ThemeLinkEvent::POST_SAVE
        );
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
        $this->eventDispatcher->dispatch(
            new ThemeLinkEvent($themeName, $key, 'form', $formSlug),
            ThemeLinkEvent::PRE_SAVE
        );

        $form = $this->formService->get($formSlug);
        $theme = $this->themeService->getThemeOrNewTheme($themeName);
        $themeForm = new ThemeForm();
        $themeForm->setForm($form);
        $themeForm->setTheme($theme);
        $themeForm->setName($key);

        $theme->getForms()->add($themeForm);

        $this->entityManager->persist($themeForm);
        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(
            new ThemeLinkEvent($themeName, $key, 'form', $formSlug),
            ThemeLinkEvent::POST_SAVE
        );
    }

    /**
     * Links the given menu with the given theme
     *
     * @param string $key
     * @param string $themeName
     * @param int $menuId
     */
    public function saveMenu(string $key, string $themeName, int $menuId): void
    {
        $this->eventDispatcher->dispatch(
            new ThemeLinkEvent($themeName, $key, 'menu', '', $menuId),
            ThemeLinkEvent::PRE_SAVE
        );

        $menu = $this->menuService->get($menuId);
        $theme = $this->themeService->getThemeOrNewTheme($themeName);
        $themeMenu = new ThemeMenu();
        $themeMenu->setMenu($menu);
        $themeMenu->setTheme($theme);
        $themeMenu->setName($key);

        $theme->getMenus()->add($themeMenu);

        $this->entityManager->persist($themeMenu);
        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(
            new ThemeLinkEvent($themeName, $key, 'menu', '', $menuId),
            ThemeLinkEvent::POST_SAVE
        );
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
        $this->eventDispatcher->dispatch(
            new ThemeLinkEvent($themeName, $key, 'artwork', $artworkSlug),
            ThemeLinkEvent::PRE_SAVE
        );

        $artwork = $this->artworkService->get($artworkSlug);
        $theme = $this->themeService->getThemeOrNewTheme($themeName);
        $themeArtwork = new ThemeArtwork();
        $themeArtwork->setArtwork($artwork);
        $themeArtwork->setTheme($theme);
        $themeArtwork->setName($key);

        $theme->getArtworks()->add($artwork);

        $this->entityManager->persist($themeArtwork);
        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(
            new ThemeLinkEvent($themeName, $key, 'artwork', $artworkSlug),
            ThemeLinkEvent::POST_SAVE
        );
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
        $this->eventDispatcher->dispatch(
            new ThemeLinkEvent($themeName, $key, 'art_gallery', $artGallerySlug),
            ThemeLinkEvent::PRE_SAVE
        );

        $gallery = $this->artGalleryService->get($artGallerySlug);
        $theme = $this->themeService->getThemeOrNewTheme($themeName);
        $themeGallery = new ThemeArtGallery();
        $themeGallery->setArtGallery($gallery);
        $themeGallery->setTheme($theme);
        $themeGallery->setName($key);

        $theme->getArtGalleries()->add($themeGallery);

        $this->entityManager->persist($themeGallery);
        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(
            new ThemeLinkEvent($themeName, $key, 'art_gallery', $artGallerySlug),
            ThemeLinkEvent::POST_SAVE
        );
    }

    /**
     * Links the given video gallery with the given theme
     *
     * @param string $key
     * @param string $themeName
     * @param string $videoGallerySlug
     */
    public function saveVideoGallery(
        string $key,
        string $themeName,
        string $videoGallerySlug
    ): void {
        $this->eventDispatcher->dispatch(
            new ThemeLinkEvent($themeName, $key, 'video_gallery', $videoGallerySlug),
            ThemeLinkEvent::PRE_SAVE
        );

        $gallery = $this->videoGalleryService->get($videoGallerySlug);
        $theme = $this->themeService->getThemeOrNewTheme($themeName);
        $themeGallery = new ThemeVideoGallery();
        $themeGallery->setVideoGallery($gallery);
        $themeGallery->setTheme($theme);
        $themeGallery->setName($key);

        $theme->getVideoGalleries()->add($themeGallery);

        $this->entityManager->persist($themeGallery);
        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(
            new ThemeLinkEvent($themeName, $key, 'video_gallery', $videoGallerySlug),
            ThemeLinkEvent::POST_SAVE
        );
    }
}
