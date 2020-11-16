<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 05.01.2018
 * Time: 21:55
 */

namespace Jinya\Framework;

use Exception;
use Jinya\Entity\Artist\User;
use Jinya\Entity\Menu\RoutingEntry;
use Jinya\Services\Twig\CompilerInterface;
use SplFileInfo;
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

abstract class BaseController
{
    private RequestStack $requestStack;

    private HttpKernelInterface $kernel;

    private AuthorizationCheckerInterface $authorizationChecker;

    private TokenStorageInterface $tokenStorage;

    private RouterInterface $router;

    private CompilerInterface $compiler;

    /**
     * BaseController constructor.
     */
    public function __construct(
        RequestStack $requestStack,
        HttpKernelInterface $kernel,
        AuthorizationCheckerInterface $authorizationChecker,
        TokenStorageInterface $tokenStorage,
        RouterInterface $router,
        CompilerInterface $compiler
    ) {
        $this->requestStack = $requestStack;
        $this->kernel = $kernel;
        $this->authorizationChecker = $authorizationChecker;
        $this->tokenStorage = $tokenStorage;
        $this->router = $router;
        $this->compiler = $compiler;
    }

    /**
     * Renders the given Twig template with the current theme
     */
    final protected function render(string $view, array $parameters = []): Response
    {
        $response = new Response();

        $response->setContent($this->compiler->compile($view, $parameters));

        return $response;
    }

    /**
     * Forwards the request to the given RoutingEntry
     *
     * @throws Exception
     * @see RoutingEntry
     */
    final protected function forwardToRoute(RoutingEntry $route): Response
    {
        $request = $this->requestStack->getCurrentRequest();

        $controller = $this->convertRouteToControllerName($route->getRouteName());
        $path = $route->getRouteParameter();

        /* @noinspection NullPointerExceptionInspection */
        $path['_forwarded'] = $request->attributes;
        $path['_controller'] = $controller;
        /** @noinspection NullPointerExceptionInspection */
        $subRequest = $request->duplicate([], null, $path);

        return $this->kernel->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
    }

    private function convertRouteToControllerName(string $routeName): string
    {
        $routes = $this->router->getRouteCollection();

        /* @noinspection NullPointerExceptionInspection */
        return $routes->get($routeName)->getDefaults()['_controller'];
    }

    /**
     * Returns a JsonResponse that uses the serializer component if enabled, or json_encode.
     *
     * @param array|string|null $data
     * @see JsonResponse
     * @see json_decode()
     */
    final protected function json($data, int $status = Response::HTTP_OK): JsonResponse
    {
        $response = new JsonResponse($data, $status);
        $response->setEncodingOptions(0);

        return $response;
    }

    /**
     * Returns a BinaryFileResponse object with original or customized file name and disposition header.
     *
     * @param SplFileInfo|string $file File object or path to file to be sent as response
     * @param string|null $fileName
     */
    final protected function file(
        $file,
        string $fileName,
        string $disposition = ResponseHeaderBag::DISPOSITION_ATTACHMENT
    ): BinaryFileResponse {
        $response = new BinaryFileResponse($file);
        $response->setContentDisposition($disposition, $fileName);

        return $response;
    }

    /**
     * Checks if the attributes are granted against the current authentication token and optionally supplied subject.
     *
     * @param array|string $attributes
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
     */
    final protected function createAccessDeniedException(): AccessDeniedException
    {
        return new AccessDeniedException('Access Denied.');
    }

    /**
     * Get a user from the Security Token Storage.
     *
     * @see TokenInterface::getUser()
     */
    final protected function getUser(): ?User
    {
        $token = $this->tokenStorage->getToken();
        if (null === $token) {
            return null;
        }

        /** @var User $user */
        $user = $token->getUser();
        if (!is_object($user)) {
            return null;
        }

        return $user;
    }
}
