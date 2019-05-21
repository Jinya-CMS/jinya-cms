<?php

namespace Jinya\Controller\Api\Theme;

use Doctrine\Common\Collections\ArrayCollection;
use Jinya\Entity\Theme\ThemeArtGallery;
use Jinya\Entity\Theme\ThemeArtwork;
use Jinya\Entity\Theme\ThemeForm;
use Jinya\Entity\Theme\ThemeMenu;
use Jinya\Entity\Theme\ThemePage;
use Jinya\Entity\Theme\ThemeVideoGallery;
use Jinya\Framework\BaseApiController;
use Jinya\Services\Artworks\ArtworkServiceInterface;
use Jinya\Services\Form\FormServiceInterface;
use Jinya\Services\Galleries\ArtGalleryServiceInterface;
use Jinya\Services\Galleries\VideoGalleryServiceInterface;
use Jinya\Services\Menu\MenuServiceInterface;
use Jinya\Services\Pages\PageServiceInterface;
use Jinya\Services\Theme\ThemeServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ThemeLinkController extends BaseApiController
{

    /**
     * @Route("/api/theme/{themeName}/menus", methods={"PUT"}, name="api_theme_put_menus")
     * @IsGranted("ROLE_WRITER")
     *
     * @param string $themeName
     * @param MenuServiceInterface $menuService
     * @param ThemeServiceInterface $themeService
     * @return Response
     */
    public function postMenusAction(
        string $themeName,
        MenuServiceInterface $menuService,
        ThemeServiceInterface $themeService
    ): Response {
        [$data, $status] = $this->tryExecute(function () use ($themeName, $menuService, $themeService) {
            $menuIds = $this->getValue('menus');
            $theme = $themeService->getTheme($themeName);
            $menus = array_map(static function ($menu) use ($theme, $menuService) {
                $themeMenu = new ThemeMenu();
                $themeMenu->setName($menu['name']);
                $themeMenu->setMenu($menuService->get($menu['id']));
                $themeMenu->setTheme($theme);

                return $themeMenu;
            }, $menuIds);

            $theme->setMenus(new ArrayCollection($menus));
            $themeService->update($theme);
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/theme/{themeName}/pages", methods={"PUT"}, name="api_theme_put_pages")
     * @IsGranted("ROLE_WRITER")
     *
     * @param string $themeName
     * @param PageServiceInterface $pageService
     * @param ThemeServiceInterface $themeService
     * @return Response
     */
    public function postPagesAction(
        string $themeName,
        PageServiceInterface $pageService,
        ThemeServiceInterface $themeService
    ): Response {
        [$data, $status] = $this->tryExecute(function () use ($themeName, $pageService, $themeService) {
            $pageIds = $this->getValue('pages');
            $theme = $themeService->getTheme($themeName);
            $pages = array_map(static function ($page) use ($theme, $pageService) {
                $themePage = new ThemePage();
                $themePage->setName($page['name']);
                $themePage->setPage($pageService->get($page['id']));
                $themePage->setTheme($theme);

                return $themePage;
            }, $pageIds);

            $theme->setMenus(new ArrayCollection($pages));
            $themeService->update($theme);
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/theme/{themeName}/artworks", methods={"PUT"}, name="api_theme_put_artworks")
     * @IsGranted("ROLE_WRITER")
     *
     * @param string $themeName
     * @param ArtworkServiceInterface $artworkService
     * @param ThemeServiceInterface $themeService
     * @return Response
     */
    public function postArtworksAction(
        string $themeName,
        ArtworkServiceInterface $artworkService,
        ThemeServiceInterface $themeService
    ): Response {
        [$data, $status] = $this->tryExecute(function () use ($themeName, $artworkService, $themeService) {
            $menuIds = $this->getValue('artworks');
            $theme = $themeService->getTheme($themeName);
            $artworks = array_map(static function ($artwork) use ($theme, $artworkService) {
                $themeArtwork = new ThemeArtwork();
                $themeArtwork->setName($artwork['name']);
                $themeArtwork->setArtwork($artworkService->get($artwork['id']));
                $themeArtwork->setTheme($theme);

                return $themeArtwork;
            }, $menuIds);

            $theme->setArtworks(new ArrayCollection($artworks));
            $themeService->update($theme);
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/theme/{themeName}/artgalleries", methods={"PUT"}, name="api_theme_put_art_galleries")
     * @IsGranted("ROLE_WRITER")
     *
     * @param string $themeName
     * @param ArtGalleryServiceInterface $artGalleryService
     * @param ThemeServiceInterface $themeService
     * @return Response
     */
    public function postArtGalleryAction(
        string $themeName,
        ArtGalleryServiceInterface $artGalleryService,
        ThemeServiceInterface $themeService
    ): Response {
        [$data, $status] = $this->tryExecute(function () use ($themeName, $artGalleryService, $themeService) {
            $artGalleryIds = $this->getValue('galleries');
            $theme = $themeService->getTheme($themeName);
            $galleries = array_map(static function ($artGallery) use ($theme, $artGalleryService) {
                $themeArtGallery = new ThemeArtGallery();
                $themeArtGallery->setName($artGallery['name']);
                $themeArtGallery->setArtGallery($artGalleryService->get($artGallery['id']));
                $themeArtGallery->setTheme($theme);

                return $themeArtGallery;
            }, $artGalleryIds);

            $theme->setArtGalleries(new ArrayCollection($galleries));
            $themeService->update($theme);
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/theme/{themeName}/videogalleries", methods={"PUT"}, name="api_theme_put_video_galleries")
     * @IsGranted("ROLE_WRITER")
     *
     * @param string $themeName
     * @param VideoGalleryServiceInterface $videoGalleryService
     * @param ThemeServiceInterface $themeService
     * @return Response
     */
    public function postVideoGalleriesAction(
        string $themeName,
        VideoGalleryServiceInterface $videoGalleryService,
        ThemeServiceInterface $themeService
    ): Response {
        [$data, $status] = $this->tryExecute(function () use ($themeName, $videoGalleryService, $themeService) {
            $menuIds = $this->getValue('galleries');
            $theme = $themeService->getTheme($themeName);
            $galleries = array_map(static function ($videoGallery) use ($theme, $videoGalleryService) {
                $themeMenu = new ThemeVideoGallery();
                $themeMenu->setName($videoGallery['name']);
                $themeMenu->setVideoGallery($videoGalleryService->get($videoGallery['id']));
                $themeMenu->setTheme($theme);

                return $themeMenu;
            }, $menuIds);

            $theme->setVideoGalleries(new ArrayCollection($galleries));
            $themeService->update($theme);
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/theme/{themeName}/forms", methods={"PUT"}, name="api_theme_put_forms")
     * @IsGranted("ROLE_WRITER")
     *
     * @param string $themeName
     * @param FormServiceInterface $formService
     * @param ThemeServiceInterface $themeService
     * @return Response
     */
    public function postFormsAction(
        string $themeName,
        FormServiceInterface $formService,
        ThemeServiceInterface $themeService
    ): Response {
        [$data, $status] = $this->tryExecute(function () use ($themeName, $formService, $themeService) {
            $menuIds = $this->getValue('forms');
            $theme = $themeService->getTheme($themeName);
            $forms = array_map(static function ($menu) use ($theme, $formService) {
                $themeMenu = new ThemeForm();
                $themeMenu->setName($menu['name']);
                $themeMenu->setForm($formService->get($menu['id']));
                $themeMenu->setTheme($theme);

                return $themeMenu;
            }, $menuIds);

            $theme->setMenus(new ArrayCollection($forms));
            $themeService->update($theme);
        });

        return $this->json($data, $status);
    }
}