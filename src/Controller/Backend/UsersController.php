<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 21.10.2017
 * Time: 19:32.
 */

namespace Jinya\Controller\Backend;

use Jinya\Form\Backend\AddUserData;
use Jinya\Form\Backend\ChangePasswordData;
use Jinya\Form\Backend\ChangePasswordType;
use Jinya\Form\Backend\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Jinya\Services\Users\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UsersController extends Controller
{
    /** @var UserServiceInterface */
    private $userService;

    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->userService = $this->get('jinya_gallery.services.user_service');
    }

    /**
     * @Route("/users", name="backend_users_index")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request): Response
    {
        return $this->redirectToRoute('backend_users_overview');
    }

    /**
     * @Route("/users/overview", name="backend_users_overview")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function overviewAction(Request $request): Response
    {
        $allUsers = $this->userService->getAllUsers(0, PHP_INT_MAX);

        return $this->render('@Backend/users/overview.html.twig', [
            'users' => $allUsers,
        ]);
    }

    /**
     * @Route("/users/add", name="backend_users_add")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function addAction(Request $request): Response
    {
        $form = $this->createForm(UserType::class, new AddUserData());

        $form->handleRequest($request);
        $viewData = [
            'form' => $form->createView(),
        ];

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var AddUserData $model */
            $model = $form->getData();
            $user = $this->userService->createUser($model);

            return $this->redirectToRoute('backend_users_details', [
                'id' => $user->getId(),
            ]);
        } elseif ($form->isSubmitted() && !$form->isValid()) {
            $viewData['error'] = !$form->isValid();
        }

        return $this->render('@Backend/users/add.html.twig', $viewData);
    }

    /**
     * @Route("/users/edit/{id}", name="backend_users_edit")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     *
     * @param Request $request
     * @param int $id
     *
     * @return Response
     */
    public function editAction(Request $request, int $id): Response
    {
        $form = $this->createForm(UserType::class);
        $user = $this->userService->getUser($id);
        $user->setProfilePicture(null);
        $form->setData($user);

        $form->handleRequest($request);
        $viewData = [
            'form' => $form->createView(),
            'id' => $id,
        ];

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var AddUserData $model */
            $model = $form->getData();
            $this->userService->updateUser($id, $model);

            return $this->redirectToRoute('backend_users_details', ['id' => $id]);
        } elseif ($form->isSubmitted() && !$form->isValid()) {
            $viewData['error'] = !$form->isValid();
        }

        return $this->render('@Backend/users/edit.html.twig', $viewData);
    }

    /**
     * @Route("/users/delete/{id}", name="backend_users_delete")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     *
     * @param Request $request
     * @param int $id
     *
     * @return Response
     */
    public function deleteAction(Request $request, int $id): Response
    {
        if ($request->getMethod() == 'POST') {
            $this->userService->deleteUser($id);

            return $this->redirectToRoute('backend_users_overview');
        }

        return $this->render('@Backend/users/delete.html.twig', [
            'id' => $id,
            'user' => $this->userService->getUser($id),
        ]);
    }

    /**
     * @Route("/users/details/{id}", name="backend_users_details")
     *
     * @param Request $request
     * @param int $id
     *
     * @return Response
     */
    public function detailsAction(Request $request, int $id): Response
    {
        return $this->render('@Backend/users/details.html.twig', [
            'id' => $id,
            'user' => $this->userService->getUser($id),
        ]);
    }

    /**
     * @Route("/users/changepassword/{id}", name="backend_users_changepassword")
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function changePasswordAction(Request $request, int $id): Response
    {
        $form = $this->createForm(ChangePasswordType::class);

        $form->handleRequest($request);
        $viewData = [
            'form' => $form->createView(),
            'id' => $id,
        ];

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ChangePasswordData $formData */
            $formData = $form->getData();

            $this->userService->changePassword($id, $formData->getPassword());

            return $this->redirectToRoute('backend_users_details', ['id' => $id]);
        } elseif ($form->isSubmitted() && !$form->isValid()) {
            $viewData['error'] = !$form->isValid();
        }

        return $this->render('@Backend/users/changePassword.html.twig', $viewData);
    }
}
