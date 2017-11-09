<?php

namespace BackendBundle\Controller;

use BackendBundle\Form\GalleryType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GalleriesController extends Controller
{

    /**
     * @Route("/galleries", name="backend_galleries_index")
     *
     * @return Response
     */
    public function indexAction()
    {
        return $this->redirectToRoute('backend_galleries_overview');
    }

    /**
     * @Route("/galleries/overview", name="backend_galleries_overview")
     *
     * @param Request $request
     * @return Response
     */
    public function overviewAction(Request $request): Response
    {
        return $this->render('@Backend/galleries/overview.html.twig');
    }

    /**
     * @Route("/galleries/get", name="backend_galleries_getAll")
     *
     * @param Request $request
     * @return Response
     */
    public function getGalleries(Request $request): Response
    {
        $galleryService = $this->get('jinya_gallery.services.gallery_service');
        $offset = $request->get('offset', 0);
        $count = $request->get('count', 12);
        $keyword = $request->get('keyword', '');
        $allData = $galleryService->getAll($offset, $count, $keyword);
        $allCount = $galleryService->countAll($keyword);

        return $this->json([
            'data' => $allData,
            'more' => $allCount > $count + $offset,
            'moreLink' => $this->generateUrl('backend_galleries_getAll', [
                'keyword' => $keyword,
                'count' => $count,
                'offset' => $offset + $count
            ])
        ]);
    }

    /**
     * @Route("/galleries/add", name="backend_galleries_add")
     *
     * @param Request $request
     * @return Response
     */
    public function addAction(Request $request): Response
    {
        $form = $this->createForm(GalleryType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $galleryService = $this->get('jinya_gallery.services.gallery_service');
            $galleryService->saveOrUpdate($data);
        }

        return $this->render('@Backend/galleries/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/galleries/edit", name="backend_galleries_edit")
     *
     * @param Request $request
     * @return Response
     */
    public function editAction(Request $request): Response
    {
        return $this->render('@Backend/galleries/edit.html.twig');
    }

    /**
     * @Route("/galleries/details", name="backend_galleries_details")
     *
     * @param Request $request
     * @return Response
     */
    public function detailsAction(Request $request): Response
    {
        return $this->render('@Backend/galleries/details.html.twig');
    }

    /**
     * @Route("/galleries/delete", name="backend_galleries_delete")
     *
     * @param Request $request
     * @return Response
     */
    public function deleteAction(Request $request): Response
    {
        return $this->render('@Backend/galleries/delete.html.twig');
    }
}
