<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 21.10.2017
 * Time: 19:32
 */

namespace BackendBundle\Controller;


use BackendBundle\Entity\User;
use BackendBundle\Form\UserData;
use BackendBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UsersController extends Controller
{
    /**
     * @Route("/users", name="backend_users_index")
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request): Response
    {
        return $this->redirectToRoute('backend_users_overview');
    }

    /**
     * @Route("/users/overview", name="backend_users_overview")
     * @param Request $request
     * @return Response
     */
    public function overviewAction(Request $request): Response
    {
        return $this->render('@Backend/users/overview.html.twig', ['ajax' => $request->isXmlHttpRequest()]);
    }

    /**
     * @Route("/users/add", name="backend_users_add")
     * @param Request $request
     * @return Response
     */
    public function addAction(Request $request): Response
    {
        $form = $this->createForm(UserType::class);

        $form->handleRequest($request);
        $viewData = [
            'ajax' => $request->isXmlHttpRequest(),
            'form' => $form->createView()
        ];

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UserData $model */
            $model = $form->getData();

            $manipulator = $this->get('jinya_gallery.service_user.user_creator');
            $user = $manipulator->create($model);

            return $this->redirectToRoute('backend_users_details', [
                'id' => $user->getId()
            ]);
        } elseif ($form->isSubmitted() && !$form->isValid()) {
            $viewData['error'] = !$form->isValid();
        }

        return $this->render('@Backend/users/add.html.twig', $viewData);
    }

    /**
     * @Route("/users/edit/{id}", name="backend_users_edit")
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function editAction(Request $request, int $id): Response
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $userRepository = $em->getRepository(User::class);
        /** @var User $user */
        $user = $userRepository->find($id);

        $userData = new UserData();
        $userData->setEmail($user->getEmail());
        $userData->setFirstname($user->getFirstname());
        $userData->setLastname($user->getLastname());
        $userData->setUsername($user->getUsername());
        $userData->setAdmin($user->hasRole(User::ROLE_ADMIN));
        $userData->setWriter($user->hasRole(User::ROLE_WRITER));
        $userData->setSuperAdmin($user->isSuperAdmin());
        $userData->setActive($user->isEnabled());

        $form = $this->createForm(UserType::class);
        $form->setData($userData);

        $form->handleRequest($request);
        $viewData = [
            'ajax' => $request->isXmlHttpRequest(),
            'form' => $form->createView()
        ];

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $model */
            $model = $form->getData();

            return $this->redirectToRoute('backend_users_details');
        } elseif ($form->isSubmitted() && !$form->isValid()) {
            $viewData['error'] = !$form->isValid();
        }

        return $this->render('@Backend/users/edit.html.twig', $viewData);
    }

    /**
     * @Route("/users/delete/{id}", name="backend_users_delete")
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function deleteAction(Request $request, int $id): Response
    {
        return $this->render('@Backend/users/delete.html.twig', ['ajax' => $request->isXmlHttpRequest()]);
    }

    /**
     * @Route("/users/details/{id}", name="backend_users_details")
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function detailsAction(Request $request, int $id): Response
    {
        return $this->render('@Backend/users/details.html.twig', ['ajax' => $request->isXmlHttpRequest()]);
    }
}