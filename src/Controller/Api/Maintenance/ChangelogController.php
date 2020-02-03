<?php

namespace Jinya\Controller\Api\Maintenance;

use Jinya\Framework\BaseApiController;
use Jinya\Services\Twig\CompilerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ChangelogController extends BaseApiController
{
    /**
     * @var string
     */
    private $jinyaVersion;

    /**
     * @var string
     */
    private $kernelProjectDir;

    /**
     * ChangelogController constructor.
     * @param string $jinyaVersion
     * @param string $kernelProjectDir
     * @param TranslatorInterface $translator
     * @param LoggerInterface $logger
     * @param UrlGeneratorInterface $urlGenerator
     * @param RequestStack $requestStack
     * @param HttpKernelInterface $kernel
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param TokenStorageInterface $tokenStorage
     * @param RouterInterface $router
     * @param CompilerInterface $compiler
     */
    public function __construct(
        string $jinyaVersion,
        string $kernelProjectDir,
        TranslatorInterface $translator,
        LoggerInterface $logger,
        UrlGeneratorInterface $urlGenerator,
        RequestStack $requestStack,
        HttpKernelInterface $kernel,
        AuthorizationCheckerInterface $authorizationChecker,
        TokenStorageInterface $tokenStorage,
        RouterInterface $router,
        CompilerInterface $compiler
    ) {
        parent::__construct(
            $translator,
            $logger,
            $urlGenerator,
            $requestStack,
            $kernel,
            $authorizationChecker,
            $tokenStorage,
            $router,
            $compiler
        );
        $this->jinyaVersion = $jinyaVersion;
        $this->kernelProjectDir = $kernelProjectDir;
    }

    /**
     * @Route("/api/changelog", name="api_changelog_get", methods={"GET"})
     *
     * @return Response
     */
    public function getAction(): Response
    {
        return $this->json([
            'changelog' => file_get_contents($this->kernelProjectDir . DIRECTORY_SEPARATOR . 'CHANGELOG.md'),
            'version' => $this->jinyaVersion,
        ]);
    }
}
