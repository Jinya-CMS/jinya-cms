<?php

namespace BackendBundle\Controller;

use BackendBundle\Form\GalleryType;
use DataBundle\Entity\Gallery;
use const PHP_INT_MAX;
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
        return $this->render('@Backend/galleries/overview.html.twig', [
            'search' => $request->get('keyword', '')
        ]);
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
        $count = PHP_INT_MAX;
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
        $form = $this->createForm(GalleryType::class, new Gallery());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $galleryService = $this->get('jinya_gallery.services.gallery_service');
            $galleryService->saveOrUpdate($data);
            return $this->redirectToRoute('backend_galleries_index');
        }

        return $this->render('@Backend/galleries/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/galleries/edit/{id}", name="backend_galleries_edit")
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function editAction(Request $request, int $id): Response
    {
        $galleryService = $this->get('jinya_gallery.services.gallery_service');
        $gallery = $galleryService->get($id);
        $form = $this->createForm(GalleryType::class, $gallery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $galleryService->saveOrUpdate($data);
            return $this->redirectToRoute('backend_galleries_details', [
                'id' => $data->getId()
            ]);
        }

        return $this->render('@Backend/galleries/edit.html.twig', [
            'form' => $form->createView(),
            'gallery' => $gallery
        ]);
    }

    /**
     * @Route("/galleries/details/{id}", name="backend_galleries_details")
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function detailsAction(Request $request, int $id): Response
    {
        $galleryService = $this->get('jinya_gallery.services.gallery_service');
        $gallery = $galleryService->get($id);
        return $this->render('@Backend/galleries/details.html.twig', [
            'gallery' => $gallery
        ]);
    }

    /**
     * @Route("/galleries/delete/{id}", name="backend_galleries_delete")
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function deleteAction(Request $request, int $id): Response
    {
        $galleryService = $this->get('jinya_gallery.services.gallery_service');
        if ($request->isMethod('POST')) {
            $galleryService->delete($id);
            return $this->redirectToRoute('backend_galleries_overview');
        }

        $gallery = $galleryService->get($id);
        return $this->render('@Backend/galleries/delete.html.twig', [
            'gallery' => $gallery
        ]);
    }

    /**
     * @Route("/galleries/background/delete/{id}", methods={"DELETE"}, name="backend_galleries_background_delete")
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function deleteBackgroundImage(Request $request, int $id): Response
    {
        $galleryService = $this->get('jinya_gallery.services.gallery_service');
        $gallery = $galleryService->get($id);
        $gallery->setBackground(null);
        $galleryService->saveOrUpdate($gallery);

        return $this->json(true);
    }

    /**
     * @Route("/galleries/history/{id}", name="backend_galleries_history")
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function historyAction(Request $request, int $id): Response
    {
        return $this->forward('BackendBundle:History:index', [
            'class' => Gallery::class,
            'id' => $id,
            'resetRoute' => 'backend_galleries_reset',
            'layout' => '@Backend/galleries/galleries_base.html.twig'
        ]);
    }

    /**
     * @Route("/galleries/history/{id}/reset", name="backend_galleries_reset", methods={"POST"})
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function resetAction(Request $request, int $id): Response
    {
        $origin = $request->get('origin');
        $galleryService = $this->get('jinya_gallery.services.gallery_service');
        $key = $request->get('key');
        $value = $request->get('value');

        $galleryService->updateField($key, $value, $id);

        return $this->redirect($origin);
    }
}
