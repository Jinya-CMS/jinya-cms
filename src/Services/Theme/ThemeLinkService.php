<?php

namespace Jinya\Services\Theme;

use Doctrine\ORM\EntityManagerInterface;
use Jinya\Entity\Theme\ThemeFile;
use Jinya\Entity\Theme\ThemeForm;
use Jinya\Entity\Theme\ThemeGallery;
use Jinya\Entity\Theme\ThemeMenu;
use Jinya\Entity\Theme\ThemePage;
use Jinya\Entity\Theme\ThemeSegmentPage;
use Jinya\Framework\Events\Theme\ThemeLinkEvent;
use Jinya\Services\Form\FormServiceInterface;
use Jinya\Services\Media\FileServiceInterface;
use Jinya\Services\Media\GalleryServiceInterface;
use Jinya\Services\Menu\MenuServiceInterface;
use Jinya\Services\Pages\PageServiceInterface;
use Jinya\Services\SegmentPages\SegmentPageServiceInterface;
use Symfony\Component\Yaml\Yaml;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ThemeLinkService implements ThemeLinkServiceInterface
{
    /** @var EntityManagerInterface */
    private EntityManagerInterface $entityManager;

    /** @var EventDispatcherInterface */
    private EventDispatcherInterface $eventDispatcher;

    /** @var string */
    private string $themeDir;

    /** @var ThemeServiceInterface */
    private ThemeServiceInterface $themeService;

    /** @var PageServiceInterface */
    private PageServiceInterface $pageService;

    /** @var SegmentPageServiceInterface */
    private SegmentPageServiceInterface $segmentPageService;

    /** @var FormServiceInterface */
    private FormServiceInterface $formService;

    /** @var MenuServiceInterface */
    private MenuServiceInterface $menuService;

    /** @var GalleryServiceInterface */
    private GalleryServiceInterface $galleryService;

    /** @var FileServiceInterface */
    private FileServiceInterface $fileService;

    /**
     * ThemeLinkService constructor.
     * @param EntityManagerInterface $entityManager
     * @param EventDispatcherInterface $eventDispatcher
     * @param string $themeDir
     * @param ThemeServiceInterface $themeService
     * @param PageServiceInterface $pageService
     * @param SegmentPageServiceInterface $segmentPageService
     * @param FormServiceInterface $formService
     * @param MenuServiceInterface $menuService
     * @param GalleryServiceInterface $galleryService
     * @param FileServiceInterface $fileService
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher,
        string $themeDir,
        ThemeServiceInterface $themeService,
        PageServiceInterface $pageService,
        SegmentPageServiceInterface $segmentPageService,
        FormServiceInterface $formService,
        MenuServiceInterface $menuService,
        GalleryServiceInterface $galleryService,
        FileServiceInterface $fileService
    ) {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->themeDir = $themeDir;
        $this->themeService = $themeService;
        $this->pageService = $pageService;
        $this->segmentPageService = $segmentPageService;
        $this->formService = $formService;
        $this->menuService = $menuService;
        $this->galleryService = $galleryService;
        $this->fileService = $fileService;
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
        $themePage = $this->entityManager->getRepository(ThemePage::class)->findOneBy(['name' => $key]);
        if (!$themePage) {
            $themePage = new ThemePage();
        }

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
        $themeSegmentPage = $this->entityManager->getRepository(ThemeSegmentPage::class)->findOneBy(['name' => $key]);
        if (!$themeSegmentPage) {
            $themeSegmentPage = new ThemeSegmentPage();
        }

        $themeSegmentPage->setSegmentPage($segmentPage);
        $themeSegmentPage->setTheme($theme);
        $themeSegmentPage->setName($key);

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
        $themeForm = $this->entityManager->getRepository(ThemeForm::class)->findOneBy(['name' => $key]);
        if (!$themeForm) {
            $themeForm = new ThemeForm();
        }

        $themeForm->setForm($form);
        $themeForm->setTheme($theme);
        $themeForm->setName($key);

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
        $themeMenu = $this->entityManager->getRepository(ThemeMenu::class)->findOneBy(['name' => $key]);
        if (!$themeMenu) {
            $themeMenu = new ThemeMenu();
        }

        $themeMenu->setMenu($menu);
        $themeMenu->setTheme($theme);
        $themeMenu->setName($key);

        $this->entityManager->persist($themeMenu);
        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(
            new ThemeLinkEvent($themeName, $key, 'menu', '', $menuId),
            ThemeLinkEvent::POST_SAVE
        );
    }

    /**
     * Links the given gallery with the given theme
     *
     * @param string $key
     * @param string $themeName
     * @param string $gallerySlug
     */
    public function saveGallery(string $key, string $themeName, string $gallerySlug): void
    {
        $this->eventDispatcher->dispatch(
            new ThemeLinkEvent($themeName, $key, 'gallery', $gallerySlug),
            ThemeLinkEvent::PRE_SAVE
        );

        $gallery = $this->galleryService->get($gallerySlug);
        $themeGallery = $this->entityManager->getRepository(ThemeGallery::class)->findOneBy(['name' => $key]);
        if (!$themeGallery) {
            $themeGallery = new ThemeGallery();
        }

        $theme = $this->themeService->getThemeOrNewTheme($themeName);
        $themeGallery->setGallery($gallery);
        $themeGallery->setTheme($theme);
        $themeGallery->setName($key);

        $this->entityManager->persist($themeGallery);
        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(
            new ThemeLinkEvent($themeName, $key, 'gallery', $gallerySlug),
            ThemeLinkEvent::POST_SAVE
        );
    }

    /**
     * Links the given file with the given theme
     *
     * @param string $key
     * @param string $themeName
     * @param int $fileId
     */
    public function saveFile(string $key, string $themeName, int $fileId): void
    {
        $this->eventDispatcher->dispatch(
            new ThemeLinkEvent($themeName, $key, 'file', '', $fileId),
            ThemeLinkEvent::PRE_SAVE
        );

        $file = $this->fileService->get($fileId);
        $theme = $this->themeService->getThemeOrNewTheme($themeName);
        $themeFile = $this->entityManager->getRepository(ThemeFile::class)->findOneBy(['name' => $key]);
        if (!$themeFile) {
            $themeFile = new ThemeFile();
        }

        $themeFile->setFile($file);
        $themeFile->setTheme($theme);
        $themeFile->setName($key);

        $this->entityManager->persist($themeFile);
        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(
            new ThemeLinkEvent($themeName, $key, 'file', '', $fileId),
            ThemeLinkEvent::POST_SAVE
        );
    }
}
