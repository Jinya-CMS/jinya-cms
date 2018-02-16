<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 06.01.2018
 * Time: 23:11
 */

namespace Jinya\Controller\Frontend;


use Jinya\Framework\BaseController;
use Symfony\Component\HttpFoundation\Response;
use const DIRECTORY_SEPARATOR;

abstract class BaseFrontendController extends BaseController
{
    public function render(string $view, array $parameters = array(), Response $response = null): Response
    {
        $themeCompilerService = $this->get('jinya_gallery.services.theme_compiler_service');
        $themeService = $this->get('jiyna_gallery.services.theme_service');
        $frontendConfigurationService = $this->get('jinya_gallery.services.frontend_configuration_service');
        $activeTheme = $frontendConfigurationService->getConfig()->getCurrentTheme();

        if (!$themeCompilerService->isCompiled($activeTheme)) {
            $themeCompilerService->compileTheme($activeTheme);
        }

        $parameters['theme']['active'] = $activeTheme;
        $parameters['theme']['path'] = $themeService->getThemeDirectory() . DIRECTORY_SEPARATOR . $activeTheme->getName() . DIRECTORY_SEPARATOR;

        return parent::render($view, $parameters, $response);
    }
}