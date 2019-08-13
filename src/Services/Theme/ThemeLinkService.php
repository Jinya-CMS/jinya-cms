<?php

namespace Jinya\Services\Theme;

use Doctrine\ORM\EntityManagerInterface;
use Jinya\Entity\Artwork\Artwork;
use Jinya\Entity\Form\Form;
use Jinya\Entity\Gallery\ArtGallery;
use Jinya\Entity\Gallery\VideoGallery;
use Jinya\Entity\Menu\Menu;
use Jinya\Entity\Page\Page;
use Jinya\Entity\SegmentPage\SegmentPage;
use Jinya\Entity\Theme\ThemeArtGallery;
use Jinya\Entity\Theme\ThemeArtwork;
use Jinya\Entity\Theme\ThemeForm;
use Jinya\Entity\Theme\ThemeMenu;
use Jinya\Entity\Theme\ThemePage;
use Jinya\Entity\Theme\ThemeSegmentPage;
use Jinya\Entity\Theme\ThemeVideoGallery;
use Jinya\Framework\Events\Theme\ThemeLinkEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Yaml\Yaml;

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

    /**
     * ThemeLinkService constructor.
     * @param EntityManagerInterface $entityManager
     * @param EventDispatcherInterface $eventDispatcher
     * @param string $themeDir
     * @param ThemeServiceInterface $themeService
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher,
        string $themeDir,
        ThemeServiceInterface $themeService
    ) {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
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
        $this->eventDispatcher->dispatch(
            ThemeLinkEvent::PRE_SAVE,
            new ThemeLinkEvent($themeName, $key, 'page', $pageSlug)
        );
        $page = $this->entityManager->getRepository(Page::class)->findOneBy(['slug' => $pageSlug]);
        $theme = $this->themeService->getThemeOrNewTheme($themeName);
        $themePage = new ThemePage();
        $themePage->setPage($page);
        $themePage->setTheme($theme);
        $themePage->setName($key);

        $theme->getPages()->add($themePage);

        $this->entityManager->persist($themePage);
        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(
            ThemeLinkEvent::POST_SAVE,
            new ThemeLinkEvent($themeName, $key, 'page', $pageSlug)
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
            ThemeLinkEvent::PRE_SAVE,
            new ThemeLinkEvent($themeName, $key, 'segment_page', $segmentPageSlug)
        );
        $segmentPage = $this->entityManager->getRepository(SegmentPage::class)->findOneBy(['slug' => $segmentPageSlug]);
        $theme = $this->themeService->getThemeOrNewTheme($themeName);
        $themeSegmentPage = new ThemeSegmentPage();
        $themeSegmentPage->setSegmentPage($segmentPage);
        $themeSegmentPage->setTheme($theme);
        $themeSegmentPage->setName($key);

        $theme->getSegmentPages()->add($themeSegmentPage);

        $this->entityManager->persist($themeSegmentPage);
        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(
            ThemeLinkEvent::POST_SAVE,
            new ThemeLinkEvent($themeName, $key, 'segment_page', $segmentPageSlug)
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
            ThemeLinkEvent::PRE_SAVE,
            new ThemeLinkEvent($themeName, $key, 'form', $formSlug)
        );

        $form = $this->entityManager->getRepository(Form::class)->findOneBy(['slug' => $formSlug]);
        $theme = $this->themeService->getThemeOrNewTheme($themeName);
        $themeForm = new ThemeForm();
        $themeForm->setForm($form);
        $themeForm->setTheme($theme);
        $themeForm->setName($key);

        $theme->getForms()->add($themeForm);

        $this->entityManager->persist($themeForm);
        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(
            ThemeLinkEvent::POST_SAVE,
            new ThemeLinkEvent($themeName, $key, 'form', $formSlug)
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
            ThemeLinkEvent::PRE_SAVE,
            new ThemeLinkEvent($themeName, $key, 'menu', '', $menuId)
        );

        $menu = $this->entityManager->getRepository(Menu::class)->findOneBy(['id' => $menuId]);
        $theme = $this->themeService->getThemeOrNewTheme($themeName);
        $themeMenu = new ThemeMenu();
        $themeMenu->setMenu($menu);
        $themeMenu->setTheme($theme);
        $themeMenu->setName($key);

        $theme->getMenus()->add($themeMenu);

        $this->entityManager->persist($themeMenu);
        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(
            ThemeLinkEvent::POST_SAVE,
            new ThemeLinkEvent($themeName, $key, 'menu', '', $menuId)
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
            ThemeLinkEvent::PRE_SAVE,
            new ThemeLinkEvent($themeName, $key, 'artwork', $artworkSlug)
        );

        $artwork = $this->entityManager->getRepository(Artwork::class)->findOneBy(['slug' => $artworkSlug]);
        $theme = $this->themeService->getThemeOrNewTheme($themeName);
        $themeArtwork = new ThemeArtwork();
        $themeArtwork->setArtwork($themeArtwork);
        $themeArtwork->setTheme($theme);
        $themeArtwork->setName($key);

        $theme->getArtworks()->add($artwork);

        $this->entityManager->persist($themeArtwork);
        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(
            ThemeLinkEvent::POST_SAVE,
            new ThemeLinkEvent($themeName, $key, 'artwork', $artworkSlug)
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
            ThemeLinkEvent::PRE_SAVE,
            new ThemeLinkEvent($themeName, $key, 'art_gallery', $artGallerySlug)
        );

        $gallery = $this->entityManager->getRepository(ArtGallery::class)->findOneBy(['slug' => $artGallerySlug]);
        $theme = $this->themeService->getThemeOrNewTheme($themeName);
        $themeGallery = new ThemeArtGallery();
        $themeGallery->setArtGallery($gallery);
        $themeGallery->setTheme($theme);
        $themeGallery->setName($key);

        $theme->getArtGalleries()->add($themeGallery);

        $this->entityManager->persist($themeGallery);
        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(
            ThemeLinkEvent::POST_SAVE,
            new ThemeLinkEvent($themeName, $key, 'art_gallery', $artGallerySlug)
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
            ThemeLinkEvent::PRE_SAVE,
            new ThemeLinkEvent($themeName, $key, 'video_gallery', $videoGallerySlug)
        );

        $gallery = $this->entityManager->getRepository(VideoGallery::class)->findOneBy(['slug' => $videoGallerySlug]);
        $theme = $this->themeService->getThemeOrNewTheme($themeName);
        $themeGallery = new ThemeVideoGallery();
        $themeGallery->setVideoGallery($gallery);
        $themeGallery->setTheme($theme);
        $themeGallery->setName($key);

        $theme->getVideoGalleries()->add($themeGallery);

        $this->entityManager->persist($themeGallery);
        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(
            ThemeLinkEvent::POST_SAVE,
            new ThemeLinkEvent($themeName, $key, 'video_gallery', $videoGallerySlug)
        );
    }
}
