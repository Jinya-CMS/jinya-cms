<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 26.10.2017
 * Time: 21:13.
 */

namespace BackendBundle\Controller;

use FOS\UserBundle\Controller\SecurityController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class LoginController extends SecurityController
{
    /**
     * @Route("/login", name="backend_login_route")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loginAction(Request $request)
    {
        $response = parent::loginAction($request);
        $response->headers->add(['login' => true]);

        return $response;
    }
}
