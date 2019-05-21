<?php

namespace Jinya\Controller\Api\Theme;

use Jinya\Formatter\Theme\ThemeFormatterInterface;
use Jinya\Framework\BaseApiController;
use Jinya\Services\Theme\ThemeLinkServiceInterface;
use Jinya\Services\Theme\ThemeServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ThemeLinkController extends BaseApiController
{

    /**
     * @Route("/api/theme/{themeName}/links", methods={"GET"}, name="api_theme_get_links")
     * @IsGranted("ROLE_WRITER")
     *
     * @param string $themeName
     * @param ThemeServiceInterface $themeService
     * @param ThemeFormatterInterface $themeFormatter
     * @return Response
     */
    public function getAction(
        string $themeName,
        ThemeServiceInterface $themeService,
        ThemeFormatterInterface $themeFormatter
    ): Response {
        [$data, $status] = $this->tryExecute(static function () use ($themeName, $themeService, $themeFormatter) {
            $theme = $themeService->getThemeOrNewTheme($themeName);

            return $themeFormatter
                ->init($theme)
                ->links()
                ->format();
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/theme/{themeName}/links/structure", methods={"GET"}, name="api_theme_get_links_structure")
     * @IsGranted("ROLE_WRITER")
     *
     * @param string $themeName
     * @param ThemeLinkServiceInterface $themeLinkService
     * @return Response
     */
    public function getStructureAction(string $themeName, ThemeLinkServiceInterface $themeLinkService): Response
    {
        [$data, $status] = $this->tryExecute(static function () use ($themeName, $themeLinkService) {
            return $themeLinkService->getLinkConfigStructure($themeName);
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/theme/{themeName}/links", methods={"PUT"}, name="api_theme_put_links")
     * @IsGranted("ROLE_WRITER")
     *
     * @param string $themeName
     * @param ThemeLinkServiceInterface $themeLinkService
     * @return Response
     */
    public function postMenusAction(string $themeName, ThemeLinkServiceInterface $themeLinkService): Response
    {
        [$data, $status] = $this->tryExecute(function () use ($themeName, $themeLinkService) {
            $menus = $this->getValue('menus', []);
            $pages = $this->getValue('pages', []);
            $forms = $this->getValue('forms', []);
            $artworks = $this->getValue('artworks', []);
            $artGalleries = $this->getValue('artGalleries', []);
            $videoGalleries = $this->getValue('videoGalleries', []);

            foreach ($menus as $menu) {
                $themeLinkService->saveMenu($menu['key'], $themeName, $menu['slug']);
            }

            foreach ($pages as $page) {
                $themeLinkService->savePage($page['key'], $themeName, $page['slug']);
            }

            foreach ($forms as $form) {
                $themeLinkService->saveForm($form['key'], $themeName, $form['slug']);
            }

            foreach ($artworks as $artwork) {
                $themeLinkService->saveMenu($artwork['key'], $themeName, $artwork['slug']);
            }

            foreach ($artGalleries as $artGallery) {
                $themeLinkService->saveMenu($artGallery['key'], $themeName, $artGallery['slug']);
            }

            foreach ($videoGalleries as $videoGallery) {
                $themeLinkService->saveMenu($videoGallery['key'], $themeName, $videoGallery['slug']);
            }
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }
}
