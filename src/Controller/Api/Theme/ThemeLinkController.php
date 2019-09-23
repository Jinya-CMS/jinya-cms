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
    public function putLinksAction(string $themeName, ThemeLinkServiceInterface $themeLinkService): Response
    {
        [$data, $status] = $this->tryExecute(function () use ($themeName, $themeLinkService) {
            $menus = $this->getValue('menus', []);
            $pages = $this->getValue('pages', []);
            $forms = $this->getValue('forms', []);
            $artworks = $this->getValue('artworks', []);
            $artGalleries = $this->getValue('artGalleries', []);
            $videoGalleries = $this->getValue('videoGalleries', []);
            $galleries = $this->getValue('galleries', []);
            $files = $this->getValue('files', []);

            foreach ($menus as $key => $menu) {
                if (array_key_exists('slug', $menu)) {
                    $themeLinkService->saveMenu($key, $themeName, $menu['slug']);
                }
            }

            foreach ($pages as $key => $page) {
                if (array_key_exists('slug', $page)) {
                    $themeLinkService->savePage($key, $themeName, $page['slug']);
                }
            }

            foreach ($forms as $key => $form) {
                if (array_key_exists('slug', $form)) {
                    $themeLinkService->saveForm($key, $themeName, $form['slug']);
                }
            }

            foreach ($artworks as $key => $artwork) {
                if (array_key_exists('slug', $artwork)) {
                    $themeLinkService->saveArtwork($key, $themeName, $artwork['slug']);
                }
            }

            foreach ($artGalleries as $key => $artGallery) {
                if (array_key_exists('slug', $artGallery)) {
                    $themeLinkService->saveArtGallery($key, $themeName, $artGallery['slug']);
                }
            }

            foreach ($videoGalleries as $key => $videoGallery) {
                if (array_key_exists('slug', $videoGallery)) {
                    $themeLinkService->saveVideoGallery($key, $themeName, $videoGallery['slug']);
                }
            }

            foreach ($galleries as $key => $gallery) {
                if (array_key_exists('slug', $gallery)) {
                    $themeLinkService->saveGallery($key, $themeName, $gallery['slug']);
                }
            }

            foreach ($files as $key => $file) {
                if (array_key_exists('id', $file)) {
                    $themeLinkService->saveGallery($key, $themeName, $file['id']);
                }
            }
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }
}
