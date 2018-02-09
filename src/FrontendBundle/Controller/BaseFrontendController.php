<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 06.01.2018
 * Time: 23:11
 */

namespace FrontendBundle\Controller;


use HelperBundle\Framework\BaseController;
use Symfony\Component\HttpFoundation\Response;
use const DIRECTORY_SEPARATOR;

abstract class BaseFrontendController extends BaseController
{
    public function render($view, array $parameters = array(), Response $response = null)
    {
        $themeService = $this->get('jinya_gallery.services.theme_service');
        $menuService = $this->get('jinya_gallery.services.menu_service');
        $activeTheme = $themeService->getActiveTheme();

        if (!$themeService->isCompiled($activeTheme)) {
            $themeService->compileTheme($activeTheme);
        }

        $parameters['theme']['active'] = $activeTheme;
        $parameters['theme']['path'] = $themeService->getThemeDirectory() . DIRECTORY_SEPARATOR . $activeTheme->getName() . DIRECTORY_SEPARATOR;

        return parent::render($view, $parameters, $response);
    }
}