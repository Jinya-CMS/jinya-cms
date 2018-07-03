<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 05.01.2018
 * Time: 21:55
 */

namespace Jinya\Framework;

use Jinya\Entity\Artist\User;
use Jinya\Entity\Menu\RoutingEntry;
use Jinya\Entity\Theme;
use Jinya\Services\Configuration\ConfigurationServiceInterface;
use Jinya\Services\Theme\ThemeCompilerServiceInterface;
use Jinya\Services\Theme\ThemeConfigServiceInterface;
use Jinya\Services\Theme\ThemeServiceInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use function str_replace;
use function strpos;

abstract class BaseController
{
    /** @var ThemeConfigServiceInterface */
    private $themeConfigService;

    /** @var ThemeServiceInterface */
    private $themeService;

    /** @var ConfigurationServiceInterface */
    private $configurationService;

    /** @var ThemeCompilerServiceInterface */
    private $themeCompilerService;

    /** @var RequestStack */
    private $requestStack;

    /** @var HttpKernelInterface */
    private $kernel;

    /** @var AuthorizationCheckerInterface */
    private $authorizationChecker;

    /** @var \Twig_Environment */
    private $twig;

    /** @var TokenStorageInterface */
    private $tokenStorage;

    /** @var RouterInterface */
    private $router;

    /**
     * BaseController constructor.
     * @param ThemeConfigServiceInterface $themeConfigService
     * @param ThemeServiceInterface $themeService
     * @param ConfigurationServiceInterface $configurationService
     * @param ThemeCompilerServiceInterface $themeCompilerService
     * @param RequestStack $requestStack
     * @param HttpKernelInterface $kernel
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param \Twig_Environment $twig
     * @param TokenStorageInterface $tokenStorage
     * @param RouterInterface $router
     */
    public function __construct(ThemeConfigServiceInterface $themeConfigService, ThemeServiceInterface $themeService, ConfigurationServiceInterface $configurationService, ThemeCompilerServiceInterface $themeCompilerService, RequestStack $requestStack, HttpKernelInterface $kernel, AuthorizationCheckerInterface $authorizationChecker, \Twig_Environment $twig, TokenStorageInterface $tokenStorage, RouterInterface $router)
    {
        $this->themeConfigService = $themeConfigService;
        $this->themeService = $themeService;
        $this->configurationService = $configurationService;
        $this->themeCompilerService = $themeCompilerService;
        $this->requestStack = $requestStack;
        $this->kernel = $kernel;
        $this->authorizationChecker = $authorizationChecker;
        $this->twig = $twig;
        $this->tokenStorage = $tokenStorage;
        $this->router = $router;
    }

    /**
     * Renders the given Twig template with the current theme
     *
     * @param string $view
     * @param array $parameters
     * @return Response
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    final protected function render(string $view, array $parameters = array()): Response
    {
        $response = new Response();

        $currentTheme = $this->configurationService->getConfig()->getCurrentTheme();

        $themeViewPath = $view;
        if (0 === strpos($view, '@Frontend')) {
            list($themeViewPath, $parameters) = $this->includeTheme($view, $parameters, $currentTheme);
        } elseif (0 === strpos($view, '@Designer')) {
            list($themeViewPath, $parameters) = $this->includeTheme($view, $parameters, $currentTheme);
        }

        $content = $this->twig->render($themeViewPath, $parameters);
        $response->setContent($content);

        return $response;
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
        $this->twig->addGlobal('themeConfig', $theme->getConfiguration());

        $parameters['theme']['active'] = $theme;
        $parameters['theme']['path'] = $this->themeService->getThemeDirectory() . DIRECTORY_SEPARATOR . $theme->getName() . DIRECTORY_SEPARATOR;

        return array($themeViewPath, $parameters);
    }

    /**
     * Forwards the request to the given RoutingEntry
     *
     * @see RoutingEntry
     * @param \Jinya\Entity\Menu\RoutingEntry $route
     * @return Response
     * @throws \Exception
     */
    final protected function forwardToRoute(RoutingEntry $route): Response
    {
        $request = $this->requestStack->getCurrentRequest();

        $controller = $this->convertRouteToControllerName($route->getRouteName());
        $path = $route->getRouteParameter();

        $path['_forwarded'] = $request->attributes;
        $path['_controller'] = $controller;
        $subRequest = $request->duplicate([], null, $path);

        return $this->kernel->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
    }

    /**
     * Returns a JsonResponse that uses the serializer component if enabled, or json_encode.
     *
     * @see json_decode()
     * @see JsonResponse
     * @param array|string|null $data
     * @param int $status
     * @return JsonResponse
     */
    final protected function json($data, int $status = Response::HTTP_OK): JsonResponse
    {
        return new JsonResponse($data, $status);
    }

    /**
     * Returns a BinaryFileResponse object with original or customized file name and disposition header.
     *
     * @param \SplFileInfo|string $file File object or path to file to be sent as response
     * @param string|null $fileName
     * @param string $disposition
     * @return BinaryFileResponse
     */
    final protected function file($file, string $fileName, string $disposition = ResponseHeaderBag::DISPOSITION_ATTACHMENT): BinaryFileResponse
    {
        $response = new BinaryFileResponse($file);
        $response->setContentDisposition($disposition, $fileName);

        return $response;
    }

    /**
     * Checks if the attributes are granted against the current authentication token and optionally supplied subject.
     *
     * @param array|string $attributes
     * @return bool
     */
    final protected function isGranted($attributes): bool
    {
        return $this->authorizationChecker->isGranted($attributes);
    }

    /**
     * Returns an AccessDeniedException.
     *
     * This will result in a 403 response code.
     *
     * @example throw $this->createAccessDeniedException('Unable to access this page!');
     *
     * @return AccessDeniedException
     */
    final protected function createAccessDeniedException(): AccessDeniedException
    {
        return new AccessDeniedException('Access Denied.');
    }

    /**
     * Get a user from the Security Token Storage.
     *
     * @return \Jinya\Entity\Artist\User|null
     * @see TokenInterface::getUser()
     */
    final protected function getUser(): ?User
    {
        $token = $this->tokenStorage->getToken();
        if (null === $token) {
            return null;
        }

        $user = $token->getUser();
        if (!is_object($user)) {
            return null;
        }

        return $user;
    }

    /**
     * @param string $routeName
     * @return string
     */
    private function convertRouteToControllerName(string $routeName): string
    {
        $routes = $this->router->getRouteCollection();

        return $routes->get($routeName)->getDefaults()['_controller'];
    }
}
