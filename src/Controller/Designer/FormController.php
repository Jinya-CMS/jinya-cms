<?php

namespace Jinya\Controller\Designer;

use Jinya\Entity\Form;
use Jinya\Entity\FormItem;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FormController extends Controller
{
    /**
     * @Route("/designer/form", name="designer_form_index")
     *
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request): Response
    {
        $formService = $this->get('jinya_gallery.services.form_service');
        $forms = $formService->getAll(0, PHP_INT_MAX, '');

        return $this->render('@Designer/form/index.html.twig', [
            'forms' => $forms
        ]);
    }

    /**
     * @Route("/designer/form/add", name="designer_form_add")
     *
     * @param Request $request
     * @return Response
     */
    public function addAction(Request $request): Response
    {
        return $this->render('@Designer/form/add.html.twig');
    }

    /**
     * @Route("/designer/form/{slug}", name="designer_form_details", methods={"GET"})
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
    public function detailsAction(string $slug, Request $request): Response
    {
        $formService = $this->get('jinya_gallery.services.form_service');
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
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function editAction(string $slug, Request $request): Response
    {
        $formService = $this->get('jinya_gallery.services.form_service');
        $form = $formService->get($slug);

        return $this->render('@Designer/form/edit.html.twig', [
            'formModel' => $form
        ]);
    }

    /**
     * @Route("/designer/form/{id}", name="designer_form_delete", methods={"DELETE"})
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

    /**
     * @Route("/designer/form/api", name="designer_form_save_form_without_id", methods={"POST"})
     * @Route("/designer/form/api/{id}", name="designer_form_save_form_with_id", methods={"PUT"})
     *
     * @param int $id
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function saveFormAction(int $id = -1, Request $request): Response
    {
        $formService = $this->get('jinya_gallery.services.form_service');
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
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function loadFormAction(int $id, Request $request): Response
    {
        $formService = $this->get('jinya_gallery.services.form_service');

        return $this->json($formService->get($id));
    }
}
