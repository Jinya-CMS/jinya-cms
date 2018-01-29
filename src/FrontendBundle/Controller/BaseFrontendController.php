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
        $themeCompilerService = $this->get('jinya_gallery.services.theme_compiler_service');
        $themeConfigService = $this->get('jiyna_gallery.services.theme_config_service');

        $activeTheme = $themeService->getActiveTheme();

        if (!$themeCompilerService->isCompiled($activeTheme)) {
            $themeCompilerService->compileTheme($activeTheme);
        }

        $parameters['theme']['active'] = $activeTheme;
        $parameters['theme']['path'] = $themeConfigService->getThemeDirectory() . DIRECTORY_SEPARATOR . $activeTheme->getName() . DIRECTORY_SEPARATOR;

        return parent::render($view, $parameters, $response);
    }
}