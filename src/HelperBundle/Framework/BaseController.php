<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 05.01.2018
 * Time: 21:55
 */

namespace HelperBundle\Framework;


use DataBundle\Entity\RoutingEntry;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use function str_replace;

abstract class BaseController extends Controller
{

    /**
     * @inheritdoc
     */
    public function render($view, array $parameters = array(), Response $response = null)
    {
        $themeService = $this->get('jinya_gallery.services.theme_service');
        $themeConfigService = $this->get('jiyna_gallery.services.theme_config_service');

        $themeService->registerThemes();
        $activeTheme = $themeService->getActiveTheme();
        $themeViewPath = $themeConfigService->getThemeNamespace($activeTheme) . str_replace('@', '/', $view);

        $parameters['themeConfig'] = $activeTheme->getConfiguration();

        $this->get('twig')->addGlobal('themeConfig', $activeTheme->getConfiguration());

        return parent::render($themeViewPath, $parameters, $response);
    }

    /**
     * Forwards the request to the given @see RoutingEntry
     *
     * @param RoutingEntry $route
     * @return Response
     */
    public function forwardToRoute(RoutingEntry $route): Response
    {
        return $this->forward($this->convertRouteToControllerName($route->getRouteName()), $route->getRouteParameter());
    }

    /**
     * @param string $routeName
     * @return string
     */
    private function convertRouteToControllerName(string $routeName): string
    {
        $routes = $this->get('router')->getRouteCollection();
        return $routes->get($routeName)->getDefaults()['_controller'];
    }
}