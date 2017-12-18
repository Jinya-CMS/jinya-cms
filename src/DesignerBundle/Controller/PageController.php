<?php

namespace DesignerBundle\Controller;

use DataBundle\Services\Pages\PageServiceInterface;
use DesignerBundle\Form\PageType;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends Controller
{
    /**
     * @Route("/page", name="designer_page_index")
     *
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request): Response
    {
        /** @var PageServiceInterface $pageService */
        $pageService = $this->get('jinya_gallery.services.page_service');
        $pages = $pageService->getAll(0, PHP_INT_MAX, '');

        return $this->render('@Designer/page/index.html.twig', [
            'pages' => $pages
        ]);
    }

    /**
     * @Route("/page/add", name="designer_page_add")
     *
     * @param Request $request
     * @return Response
     * @throws \DataBundle\Exceptions\EmptySlugException
     */
    public function addAction(Request $request): Response
    {
        $form = $this->createForm(PageType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $pageService = $this->get('jinya_gallery.services.page_service');
            $pageService->save($data);

            return $this->redirectToRoute('designer_page_index');
        }

        return $this->render('@Designer/page/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/page/{slug}/edit", name="designer_page_edit")
     *
     * @param string $slug
     * @param Request $request
     * @return Response
     * @throws \DataBundle\Exceptions\EmptySlugException
     */
    public function editAction(string $slug, Request $request): Response
    {
        $pageService = $this->get('jinya_gallery.services.page_service');
        $page = $pageService->get($slug);

        $form = $this->createForm(PageType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $pageService->save($data);

            return $this->redirectToRoute('designer_page_details', ['slug' => $slug]);
        }

        return $this->render('@Designer/page/edit.html.twig', [
            'form' => $form->createView(),
            'page' => $page
        ]);
    }

    /**
     * @Route("/page/{slug}", name="designer_page_details", methods={"GET"})
     *
     * @param string $slug
     * @param Request $request
     * @return Response
     */
    public function detailsAction(string $slug, Request $request): Response
    {
        $pageService = $this->get('jinya_gallery.services.page_service');
        $page = $pageService->get($slug);

        return $this->render('@Designer/page/details.html.twig', [
            'page' => $page
        ]);
    }

    /**
     * @Route("/page/{id}", name="designer_page_delete", methods={"DELETE"})
     *
     * @param string $id
     * @param Request $request
     * @return Response
     */
    public function deleteAction(string $id, Request $request): Response
    {
        $pageService = $this->get('jinya_gallery.services.page_service');
        try {
            $pageService->delete($id);

            return $this->json(['success' => true]);
        } catch (Exception $exception) {
            return $this->json([
                'success' => false,
                'message' => $exception->getMessage()
            ]);
        }
    }
}
