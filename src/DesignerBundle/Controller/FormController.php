<?php

namespace DesignerBundle\Controller;

use DataBundle\Services\Form\FormServiceInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FormController extends Controller
{
    /**
     * @Route("/form", name="designer_form_index")
     *
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request): Response
    {
        /** @var FormServiceInterface $formService */
        $formService = $this->get('jinya_gallery.services.form_service');
        $forms = $formService->getAll(0, PHP_INT_MAX, '');

        return $this->render('@Designer/form/index.html.twig', [
            'forms' => $forms
        ]);
    }

    /**
     * @Route("/form/add", name="designer_form_add")
     *
     * @param Request $request
     * @return Response
     */
    public function addAction(Request $request): Response
    {
        return $this->render('@Designer/form/add.html.twig');
    }

    /**
     * @Route("/form/{slug}", name="designer_form_details")
     *
     * @param Request $request
     * @return Response
     */
    public function detailsAction(string $slug, Request $request): Response
    {
        $formService = $this->get('jinya_gallery.services.form_service');
        $form = $formService->get($slug);

        return $this->render('@Designer/form/details.html.twig', [
            'form' => $form
        ]);
    }

    /**
     * @Route("/form/{slug}/edit", name="designer_form_edit")
     *
     * @param Request $request
     * @return Response
     */
    public function editAction(string $slug, Request $request): Response
    {
        $formService = $this->get('jinya_gallery.services.form_service');
        $form = $formService->get($slug);

        return $this->render('@Designer/form/details.html.twig', [
            'form' => $form
        ]);
    }

    /**
     * @Route("/form/{id}", name="designer_form_delete", methods={"DELETE"})
     *
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function deleteAction(int $id, Request $request): Response
    {
        $formService = $this->get('jinya_gallery.services.form_service');
        try {
            $formService->delete($id);

            return $this->json(['success' => true]);
        } catch (Exception $exception) {
            return $this->json([
                'success' => false,
                'message' => $exception->getMessage()
            ]);
        }
    }
}
