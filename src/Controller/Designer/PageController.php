<?php

namespace Jinya\Controller\Designer;

use Exception;
use Jinya\Form\Designer\PageType;
use Jinya\Framework\BaseController;
use Jinya\Services\Pages\PageServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends BaseController
{
    /**
     * @Route("/designer/page", name="designer_page_index")
     *
     * @param PageServiceInterface $pageService
     * @return Response
     */
    public function indexAction(PageServiceInterface $pageService): Response
    {
        $pages = $pageService->getAll(0, PHP_INT_MAX, '');

        return $this->render('@Designer/page/index.html.twig', [
            'pages' => $pages
        ]);
    }

    /**
     * @Route("/designer/page/add", name="designer_page_add")
     *
     * @param Request $request
     * @return Response
     */
    public function addAction(Request $request, PageServiceInterface $pageService): Response
    {
        $form = $this->createForm(PageType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $pageService->save($data);

            return $this->redirectToRoute('designer_page_index');
        }

        return $this->render('@Designer/page/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/designer/page/{slug}/edit", name="designer_page_edit")
     *
     * @param string $slug
     * @param Request $request
     * @param PageServiceInterface $pageService
     * @return Response
     */
    public function editAction(string $slug, Request $request, PageServiceInterface $pageService): Response
    {
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
     * @Route("/designer/page/{slug}", name="designer_page_details", methods={"GET"})
     *
     * @param string $slug
     * @param PageServiceInterface $pageService
     * @return Response
     */
    public function detailsAction(string $slug, PageServiceInterface $pageService): Response
    {
        return $this->render('@Designer/page/details.html.twig', [
            'page' => $pageService->get($slug)
        ]);
    }

    /**
     * @Route("/designer/page/{id}", name="designer_page_delete", methods={"DELETE"})
     *
     * @param string $id
     * @param PageServiceInterface $pageService
     * @return Response
     */
    public function deleteAction(string $id, PageServiceInterface $pageService): Response
    {
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
