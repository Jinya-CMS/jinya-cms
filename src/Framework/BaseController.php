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
    /** @var RequestStack */
    private $requestStack;

    /** @var HttpKernelInterface */
    private $kernel;

    /** @var AuthorizationCheckerInterface */
    private $authorizationChecker;

    /** @var TokenStorageInterface */
    private $tokenStorage;

    /** @var RouterInterface */
    private $router;

    /** @var CompilerInterface */
    private $compiler;

    /**
     * BaseController constructor.
     * @param RequestStack $requestStack
     * @param HttpKernelInterface $kernel
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param TokenStorageInterface $tokenStorage
     * @param RouterInterface $router
     * @param CompilerInterface $compiler
     */
    public function __construct(RequestStack $requestStack, HttpKernelInterface $kernel, AuthorizationCheckerInterface $authorizationChecker, TokenStorageInterface $tokenStorage, RouterInterface $router, CompilerInterface $compiler)
    {
        $this->requestStack = $requestStack;
        $this->kernel = $kernel;
        $this->authorizationChecker = $authorizationChecker;
        $this->tokenStorage = $tokenStorage;
        $this->router = $router;
        $this->compiler = $compiler;
    }

    /**
     * Renders the given Twig template with the current theme
     *
     * @param string $view
     * @param array $parameters
     * @return Response
     */
    final protected function render(string $view, array $parameters = array()): Response
    {
        $response = new Response();

        $response->setContent($this->compiler->compile($view, $parameters));

        return $response;
    }

    /**
     * Forwards the request to the given RoutingEntry
     *
     * @param RoutingEntry $route
     * @return Response
     * @throws Exception
     * @see RoutingEntry
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
     * @param string $routeName
     * @return string
     */
    private function convertRouteToControllerName(string $routeName): string
    {
        $routes = $this->router->getRouteCollection();

        return $routes->get($routeName)->getDefaults()['_controller'];
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
     * @param SplFileInfo|string $file File object or path to file to be sent as response
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
     * @return User|null
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
