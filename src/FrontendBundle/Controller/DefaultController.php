<?php

namespace FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class DefaultController extends BaseFrontendController
{
    /**
     * @Route("/{route}", name="frontend_default_index", requirements={"route": ".*"})
     *
     * @param string $route
     * @return Response
     * @throws Throwable
     */
    public function indexAction(string $route): Response
    {
        $routeService = $this->get('jinya_gallery.services.route_service');
        try {
            $routeEntry = $routeService->findByUrl('/' . $route);

            return $this->forwardToRoute($routeEntry);
        } catch (Throwable $throwable) {
            if (empty($route)) {
                return $this->render('@Frontend/Default/index.html.twig');
            } else {
                $logger = $this->get('logger');
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
     * @param string $slug
     * @return Response
     */
    public function artworkDetailAction(string $slug): Response
    {
        $artworkService = $this->get('jinya_gallery.services.artwork_service');
        $artwork = $artworkService->get($slug);

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
        $galleryService = $this->get('jinya_gallery.services.gallery_service');
        $gallery = $galleryService->get($slug);

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
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function formDetailAction(string $slug, Request $request): Response
    {
        $formService = $this->get('jinya_gallery.services.form_service');
        $formEntity = $formService->get($slug);

        $formGenerator = $this->get('jinya_gallery.services.form_generator');
        $form = $formGenerator->generateForm($formEntity);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $swift = $this->get('swiftmailer.mailer');

        }

        return $this->render('@Frontend/Form/detail.html.twig', [
            'formEntity' => $formEntity,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/page/{slug}", name="frontend_page_details")
     *
     * @param string $slug
     * @return Response
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function pageDetailAction(string $slug): Response
    {
        $pageService = $this->get('jinya_gallery.services.page_service');
        $page = $pageService->get($slug);

        return $this->render('@Frontend/Page/detail.html.twig', [
            'page' => $page
        ]);
    }
}
