<?php

namespace DesignerBundle\Controller;

use DataBundle\Services\Galleries\GalleryServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use const PHP_INT_MAX;

class GalleryController extends Controller
{
    /**
     * @Route("/gallery/", name="designer_gallery_index")
     *
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request): Response
    {
        /** @var GalleryServiceInterface $galleryService */
        $galleryService = $this->get('jinya_gallery.services.gallery_service');
        $galleries = $galleryService->getAll(0, PHP_INT_MAX, '');

        return $this->render('@Designer/gallery/index.html.twig', [
            'galleries' => $galleries
        ]);
    }

    /**
     * @Route("/gallery/{gallerySlug}", name="designer_gallery_details")
     *
     * @param Request $request
     * @param string $gallerySlug
     * @return Response
     */
    public function detailsAction(Request $request, string $gallerySlug): Response
    {
        /** @var GalleryServiceInterface $galleryService */
        $galleryService = $this->get('jinya_gallery.services.gallery_service');
        $gallery = $galleryService->get($gallerySlug);

        if ($gallery === null) {
            throw $this->createNotFoundException();
        }

        return $this->render('@Designer/gallery/details.html.twig', [
            'gallery' => $gallery
        ]);
    }
}
