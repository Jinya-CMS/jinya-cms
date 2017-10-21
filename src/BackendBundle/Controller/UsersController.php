<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 21.10.2017
 * Time: 19:32
 */

namespace BackendBundle\Controller;


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
        return $this->render('@Backend/users/add.html.twig', ['ajax' => $request->isXmlHttpRequest()]);
    }

    /**
     * @Route("/users/edit/{id}", name="backend_users_edit")
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function editAction(Request $request, int $id): Response
    {
        return $this->render('@Backend/users/edit.html.twig', ['ajax' => $request->isXmlHttpRequest()]);
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