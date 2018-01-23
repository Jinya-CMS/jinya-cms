<?php

namespace DesignerBundle\Controller;

use DataBundle\Services\Theme\ThemeServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ThemeController extends Controller
{
    /**
     * @Route("/theme/", name="designer_theme_index")
     *
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request): Response
    {
        /** @var ThemeServiceInterface $themeService */
        $themeService = $this->get('jinya_gallery.services.theme_service');
        $themes = $themeService->getAllThemes();

        return $this->render('@Designer/theme/index.html.twig', [
            'themes' => $themes
        ]);
    }

    /**
     * @Route("/theme/{name}", name="designer_theme_config")
     *
     * @param string $name
     * @param Request $request
     * @return Response
     */
    public function configAction(string $name, Request $request): Response
    {
        $themeService = $this->get('jinya_gallery.services.theme_service');

        if ($request->isMethod('POST')) {
            $themeService->saveConfig($request->get('config'), $name);
        }

        $configForm = $themeService->getConfigForm($name);
        $config = $themeService->getTheme($name)->getConfiguration();

        return $this->render('@Designer/theme/config.html.twig', [
            'configForm' => $configForm,
            'config' => $config,
            'theme' => $themeService->getTheme($name)
        ]);
    }
}
