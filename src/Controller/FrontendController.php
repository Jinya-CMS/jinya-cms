<?php

namespace Jinya\Controller;

use ArrayIterator;
use Doctrine\Common\Collections\ArrayCollection;
use Jinya\Components\Form\FormGeneratorInterface;
use Jinya\Entity\ArtworkPosition;
use Jinya\Entity\Form;
use Jinya\Framework\BaseController;
use Jinya\Services\Artworks\ArtworkServiceInterface;
use Jinya\Services\Form\FormServiceInterface;
use Jinya\Services\Galleries\GalleryServiceInterface;
use Jinya\Services\Mailing\MailerServiceInterface;
use Jinya\Services\Pages\PageServiceInterface;
use Jinya\Services\Routing\RouteServiceInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class FrontendController extends BaseController
{
    /**
     * @Route("/{route}", name="frontend_default_index", requirements={"route": "^(?!api\\/|designer\\/).*"})
     *
     * @param string                $route
     * @param RouteServiceInterface $routeService
     * @param LoggerInterface       $logger
     *
     * @return Response
     *
     * @throws Throwable
     */
    public function indexAction(string $route, RouteServiceInterface $routeService, LoggerInterface $logger): Response
    {
        try {
            $routeEntry = $routeService->findByUrl('/'.$route);

            return $this->forwardToRoute($routeEntry);
        } catch (Throwable $throwable) {
            if (empty($route)) {
                return $this->render('@Frontend/Default/index.html.twig');
            } else {
                $logger->error("Failed to load route $route");
                $logger->error($throwable->getMessage());
                $logger->error($throwable->getTraceAsString());

                throw $throwable;
            }
        }
    }

    /**
     * @Route("/artwork/{slug}", name="frontend_artwork_details")
     *
     * @param string                  $slug
     * @param ArtworkServiceInterface $artworkService
     *
     * @return Response
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function artworkDetailAction(string $slug, ArtworkServiceInterface $artworkService): Response
    {
        $artwork = $artworkService->get($slug);

        return $this->render('@Frontend/Artwork/detail.html.twig', [
            'artwork' => $artwork,
        ]);
    }

    /**
     * @Route("/gallery/{slug}", name="frontend_gallery_details")
     *
     * @param string                  $slug
     * @param GalleryServiceInterface $galleryService
     *
     * @return Response
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function galleryDetailAction(string $slug, GalleryServiceInterface $galleryService): Response
    {
        $gallery = $galleryService->get($slug);

        $artworks = $gallery->getArtworks();

        /** @var ArrayIterator $iterator */
        $iterator = $artworks->getIterator();
        $iterator->uasort(function (ArtworkPosition $a, ArtworkPosition $b) {
            return ($a->getPosition() < $b->getPosition()) ? -1 : 1;
        });
        $gallery->setArtworks(new ArrayCollection(iterator_to_array($iterator)));

        return $this->render('@Frontend/Gallery/detail.html.twig', [
            'gallery' => $gallery,
        ]);
    }

    /**
     * @Route("/form/{slug}", name="frontend_form_details")
     *
     * @param string                 $slug
     * @param Request                $request
     * @param FormServiceInterface   $formService
     * @param FormGeneratorInterface $formGenerator
     * @param MailerServiceInterface $mailerService
     *
     * @return Response
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function formDetailAction(string $slug, Request $request, FormServiceInterface $formService, FormGeneratorInterface $formGenerator, MailerServiceInterface $mailerService): Response
    {
        /** @var Form $formEntity */
        $formEntity = $formService->get($slug);

        $form = $formGenerator->generateForm($formEntity);

        $form->handleRequest($request);

        $viewData = [
            'formEntity' => $formEntity,
            'form' => $form->createView(),
        ];

        if ($form->isSubmitted() && $form->isValid()) {
            $mailerService->sendMail($formEntity, $form->getData());
            $viewData['mail_sent'] = true;
        }

        return $this->render('@Frontend/Form/detail.html.twig', $viewData);
    }

    /**
     * @Route("/page/{slug}", name="frontend_page_details")
     *
     * @param string               $slug
     * @param PageServiceInterface $pageService
     *
     * @return Response
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function pageDetailAction(string $slug, PageServiceInterface $pageService): Response
    {
        $page = $pageService->get($slug);

        return $this->render('@Frontend/Page/detail.html.twig', [
            'page' => $page,
        ]);
    }
}
