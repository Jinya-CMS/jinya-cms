<?php

namespace Jinya\Controller\Designer;

use Exception;
use Jinya\Entity\Form;
use Jinya\Entity\FormItem;
use Jinya\Framework\BaseController;
use Jinya\Services\Form\FormServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FormController extends BaseController
{
    /**
     * @Route("/designer/form", name="designer_form_index")
     *
     * @param FormServiceInterface $formService
     * @return Response
     */
    public function indexAction(FormServiceInterface $formService): Response
    {
        $forms = $formService->getAll(0, PHP_INT_MAX, '');

        return $this->render('@Designer/form/index.html.twig', [
            'forms' => $forms
        ]);
    }

    /**
     * @Route("/designer/form/add", name="designer_form_add")
     *
     * @return Response
     */
    public function addAction(): Response
    {
        return $this->render('@Designer/form/add.html.twig');
    }

    /**
     * @Route("/designer/form/{slug}", name="designer_form_details", methods={"GET"})
     *
     * @param string $slug
     * @param FormServiceInterface $formService
     * @return Response
     */
    public function detailsAction(string $slug, FormServiceInterface $formService): Response
    {
        $form = $formService->get($slug);
        $formGenerator = $this->get('jinya_gallery.components.form_generator');

        return $this->render('@Designer/form/details.html.twig', [
            'formModel' => $form,
            'form' => $formGenerator->generateForm($form)->createView()
        ]);
    }

    /**
     * @Route("/designer/form/{slug}/edit", name="designer_form_edit")
     *
     * @param string $slug
     * @param FormServiceInterface $formService
     * @return Response
     */
    public function editAction(string $slug, FormServiceInterface $formService): Response
    {
        $form = $formService->get($slug);

        return $this->render('@Designer/form/edit.html.twig', [
            'formModel' => $form
        ]);
    }

    /**
     * @Route("/designer/form/{id}", name="designer_form_delete", methods={"DELETE"})
     *
     * @param int $id
     * @param FormServiceInterface $formService
     * @return Response
     */
    public function deleteAction(int $id, FormServiceInterface $formService): Response
    {
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

    /**
     * @Route("/designer/form/api", name="designer_form_save_form_without_id", methods={"POST"})
     * @Route("/designer/form/api/{id}", name="designer_form_save_form_with_id", methods={"PUT"})
     *
     * @param int $id
     * @param Request $request
     * @param FormServiceInterface $formService
     * @return Response
     */
    public function saveFormAction(int $id = -1, Request $request, FormServiceInterface $formService): Response
    {
        if ($id > -1) {
            $form = $formService->get($id);
        } else {
            $form = new Form();
        }

        $postedForm = json_decode($request->getContent(), true);

        $items = [];
        foreach ($postedForm['items'] as $item) {
            $formItem = new FormItem();
            $formItem->setHelpText(array_key_exists('helpText', $item) ? $item['helpText'] : '');
            $formItem->setOptions(array_key_exists('options', $item) ? $item['options'] : []);
            $formItem->setLabel(array_key_exists('label', $item) ? $item['label'] : '');
            $formItem->setType(array_key_exists('type', $item) ? $item['type'] : '');
            $items[] = $formItem;
        }

        $form->setTitle($postedForm['title']);
        $form->setDescription(array_key_exists('description', $postedForm) ? $postedForm['description'] : '');
        $form->setToAddress($postedForm['toAddress']);
        $form->setSlug(array_key_exists('slug', $postedForm) ? $postedForm['slug'] : '');

        foreach ($items as $item) {
            $item->setForm($form);
            $form->getItems()->add($item);
        }

        try {
            $form = $formService->save($form);
            $router = $this->get('router');

            return $this->json([
                'success' => true,
                'redirectTarget' => $router->generate('designer_form_details', ['slug' => $form->getSlug()])
            ]);
        } catch (Exception $exception) {
            return $this->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @Route("/designer/form/api/{id}", name="designer_form_load_form", methods={"GET"})
     *
     * @param int $id
     * @param FormServiceInterface $formService
     * @return Response
     */
    public function loadFormAction(int $id, FormServiceInterface $formService): Response
    {
        return $this->json($formService->get($id));
    }
}
