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
        return $this->render('@Backend/users/index.html.twig');
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
}