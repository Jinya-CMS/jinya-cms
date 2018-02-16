<?php

namespace Jinya\Controller;

use Jinya\Components\Form\FormGeneratorInterface;
use Jinya\Entity\Form;
use Jinya\Framework\BaseController;
use Jinya\Services\Artworks\ArtworkServiceInterface;
use Jinya\Services\Configuration\ConfigurationServiceInterface;
use Jinya\Services\Form\FormServiceInterface;
use Jinya\Services\Galleries\GalleryServiceInterface;
use Jinya\Services\Mailing\MailerServiceInterface;
use Jinya\Services\Pages\PageServiceInterface;
use Jinya\Services\Routing\RouteServiceInterface;
use Jinya\Services\Theme\ThemeCompilerServiceInterface;
use Jinya\Services\Theme\ThemeConfigServiceInterface;
use Jinya\Services\Theme\ThemeServiceInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class FrontendController extends BaseController
{
    /** @var RouteServiceInterface */
    private $routeService;
    /** @var LoggerInterface */
    private $logger;
    /** @var ArtworkServiceInterface */
    private $artworkService;
    /** @var GalleryServiceInterface */
    private $galleryService;
    /** @var FormServiceInterface */
    private $formService;
    /** @var PageServiceInterface */
    private $pageService;
    /** @var FormGeneratorInterface */
    private $formGenerator;
    /** @var MailerServiceInterface */
    private $mailerService;
    /** @var string */
    private $kernelProjectDir;

    /**
     * FrontendController constructor.
     * @param RouteServiceInterface $routeService
     * @param LoggerInterface $logger
     * @param ArtworkServiceInterface $artworkService
     * @param GalleryServiceInterface $galleryService
     * @param FormServiceInterface $formService
     * @param PageServiceInterface $pageService
     * @param FormGeneratorInterface $formGenerator
     * @param MailerServiceInterface $mailerService
     * @param ThemeConfigServiceInterface $themeConfigService
     * @param ThemeServiceInterface $themeService
     * @param ConfigurationServiceInterface $frontendConfigurationService
     * @param ThemeCompilerServiceInterface $themeCompilerService
     * @param string $kernelProjectDir
     */
    public function __construct(RouteServiceInterface $routeService, LoggerInterface $logger, ArtworkServiceInterface $artworkService, GalleryServiceInterface $galleryService, FormServiceInterface $formService, PageServiceInterface $pageService, FormGeneratorInterface $formGenerator, MailerServiceInterface $mailerService, ThemeConfigServiceInterface $themeConfigService, ThemeServiceInterface $themeService, ConfigurationServiceInterface $frontendConfigurationService, ThemeCompilerServiceInterface $themeCompilerService, string $kernelProjectDir)
    {
        parent::__construct($themeConfigService, $themeService, $frontendConfigurationService, $themeCompilerService);
        $this->routeService = $routeService;
        $this->logger = $logger;
        $this->artworkService = $artworkService;
        $this->galleryService = $galleryService;
        $this->formService = $formService;
        $this->pageService = $pageService;
        $this->formGenerator = $formGenerator;
        $this->mailerService = $mailerService;
        $this->kernelProjectDir = $kernelProjectDir;
    }

    /**
     * @Route("/{route}", name="frontend_default_index", requirements={"route": ".*"})
     *
     * @param string $route
     * @return Response
     * @throws Throwable
     */
    public function indexAction(string $route): Response
    {
        try {
            $routeEntry = $this->routeService->findByUrl('/' . $route);

            return $this->forwardToRoute($routeEntry);
        } catch (Throwable $throwable) {
            if (empty($route)) {
                return $this->render('@Frontend/Default/index.html.twig');
            } else {
                $this->logger->error("Failed to load route $route");
                $this->logger->error($throwable->getMessage());
                $this->logger->error($throwable->getTraceAsString());

                throw $throwable;
            }
        }
    }

    /**
     * @Route("/artwork/{slug}", name="frontend_artwork_details")
     *
     * @param string $slug
     * @return Response
     */
    public function artworkDetailAction(string $slug): Response
    {
        $artwork = $this->artworkService->get($slug);

        return $this->render('@Frontend/Artwork/detail.html.twig', [
            'artwork' => $artwork
        ]);
    }

    /**
     * @Route("/gallery/{slug}", name="frontend_gallery_details")
     *
     * @param string $slug
     * @return Response
     */
    public function galleryDetailAction(string $slug): Response
    {
        $gallery = $this->galleryService->get($slug);

        return $this->render('@Frontend/Gallery/detail.html.twig', [
            'gallery' => $gallery
        ]);
    }

    /**
     * @Route("/form/{slug}", name="frontend_form_details")
     *
     * @param string $slug
     * @param Request $request
     * @return Response
     */
    public function formDetailAction(string $slug, Request $request): Response
    {
        /** @var Form $formEntity */
        $formEntity = $this->formService->get($slug);

        $form = $this->formGenerator->generateForm($formEntity);

        $form->handleRequest($request);

        $viewData = [
            'formEntity' => $formEntity,
            'form' => $form->createView()
        ];

        if ($form->isSubmitted() && $form->isValid()) {
            $this->mailerService->sendMail($formEntity, $form->getData());
            $viewData['mail_sent'] = true;
        }

        return $this->render('@Frontend/Form/detail.html.twig', $viewData);
    }

    /**
     * @Route("/page/{slug}", name="frontend_page_details")
     *
     * @param string $slug
     * @return Response
     */
    public function pageDetailAction(string $slug): Response
    {
        $page = $this->pageService->get($slug);

        return $this->render('@Frontend/Page/detail.html.twig', [
            'page' => $page
        ]);
    }
}
