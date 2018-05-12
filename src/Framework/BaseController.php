<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 05.01.2018
 * Time: 21:55
 */

namespace Jinya\Framework;


use Jinya\Entity\RoutingEntry;
use Jinya\Entity\Theme;
use Jinya\Services\Configuration\ConfigurationServiceInterface;
use Jinya\Services\Theme\ThemeCompilerServiceInterface;
use Jinya\Services\Theme\ThemeConfigServiceInterface;
use Jinya\Services\Theme\ThemeServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use function str_replace;
use function strpos;

abstract class BaseController extends AbstractController
{
    /** @var ThemeConfigServiceInterface */
    private $themeConfigService;
    /** @var ThemeServiceInterface */
    private $themeService;
    /** @var ConfigurationServiceInterface */
    private $configurationService;
    /** @var ThemeCompilerServiceInterface */
    private $themeCompilerService;

    /**
     * BaseController constructor.
     * @param ThemeConfigServiceInterface $themeConfigService
     * @param ThemeServiceInterface $themeService
     * @param ConfigurationServiceInterface $configurationService
     * @param ThemeCompilerServiceInterface $themeCompilerService
     */
    public function __construct(ThemeConfigServiceInterface $themeConfigService, ThemeServiceInterface $themeService, ConfigurationServiceInterface $configurationService, ThemeCompilerServiceInterface $themeCompilerService)
    {
        $this->themeConfigService = $themeConfigService;
        $this->themeService = $themeService;
        $this->configurationService = $configurationService;
        $this->themeCompilerService = $themeCompilerService;
    }

    /**
     * @inheritdoc
     */
    public function render(string $view, array $parameters = array(), Response $response = null): Response
    {
        $currentTheme = $this->configurationService->getConfig()->getCurrentTheme();

        $themeViewPath = $view;
        if (strpos($view, '@Frontend') === 0) {
            list($themeViewPath, $parameters) = $this->includeTheme($view, $parameters, $currentTheme);
        } elseif (strpos($view, '@Designer') === 0) {
            list($themeViewPath, $parameters) = $this->includeTheme($view, $parameters, $currentTheme);
        }

        return parent::render($themeViewPath, $parameters, $response);
    }

    /**
     * @param string $view
     * @param array $parameters
     * @param $theme
     * @return array
     */
    private function includeTheme(string $view, array $parameters, Theme $theme): array
    {
        if (!$this->themeCompilerService->isCompiled($theme)) {
            $this->themeCompilerService->compileTheme($theme);
        }

        $this->themeService->registerThemes();
        $themeViewPath = $this->themeConfigService->getThemeNamespace($theme) . str_replace('@', '/', $view);

        $parameters['themeConfig'] = $theme->getConfiguration();
        $this->get('twig')->addGlobal('themeConfig', $theme->getConfiguration());

        $parameters['theme']['active'] = $theme;
        $parameters['theme']['path'] = $this->themeService->getThemeDirectory() . DIRECTORY_SEPARATOR . $theme->getName() . DIRECTORY_SEPARATOR;

        return array($themeViewPath, $parameters);
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