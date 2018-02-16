<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 05.01.2018
 * Time: 21:55
 */

namespace Jinya\Framework;


use Jinya\Entity\RoutingEntry;
use Jinya\Services\Configuration\FrontendConfigurationServiceInterface;
use Jinya\Services\Theme\ThemeCompilerServiceInterface;
use Jinya\Services\Theme\ThemeConfigServiceInterface;
use Jinya\Services\Theme\ThemeServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use function str_replace;

abstract class BaseController extends Controller
{
    /** @var ThemeConfigServiceInterface */
    private $themeConfigService;
    /** @var ThemeServiceInterface */
    private $themeService;
    /** @var FrontendConfigurationServiceInterface */
    private $frontendConfigurationService;
    /** @var ThemeCompilerServiceInterface */
    private $themeCompilerService;

    /**
     * BaseController constructor.
     * @param ThemeConfigServiceInterface $themeConfigService
     * @param ThemeServiceInterface $themeService
     * @param FrontendConfigurationServiceInterface $frontendConfigurationService
     * @param ThemeCompilerServiceInterface $themeCompilerService
     */
    public function __construct(ThemeConfigServiceInterface $themeConfigService, ThemeServiceInterface $themeService, FrontendConfigurationServiceInterface $frontendConfigurationService, ThemeCompilerServiceInterface $themeCompilerService)
    {
        $this->themeConfigService = $themeConfigService;
        $this->themeService = $themeService;
        $this->frontendConfigurationService = $frontendConfigurationService;
        $this->themeCompilerService = $themeCompilerService;
    }

    /**
     * @inheritdoc
     */
    public function render(string $view, array $parameters = array(), Response $response = null): Response
    {
        $activeTheme = $this->frontendConfigurationService->getConfig()->getCurrentTheme();

        if (!$this->themeCompilerService->isCompiled($activeTheme)) {
            $this->themeCompilerService->compileTheme($activeTheme);
        }

        $activeTheme = $this->frontendConfigurationService->getConfig()->getCurrentTheme();

        $this->themeService->registerThemes();
        $themeViewPath = $this->themeConfigService->getThemeNamespace($activeTheme) . str_replace('@', '/', $view);

        $parameters['themeConfig'] = $activeTheme->getConfiguration();
        $parameters['theme']['active'] = $activeTheme;
        $parameters['theme']['path'] = $this->themeService->getThemeDirectory() . DIRECTORY_SEPARATOR . $activeTheme->getName() . DIRECTORY_SEPARATOR;

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