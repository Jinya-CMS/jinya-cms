<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 26.10.2017
 * Time: 21:13.
 */

namespace Jinya\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AccountController extends Controller
{
    /**
     * @Route("/account/login", name="jinya_account_login")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function loginAction(Request $request): Response
    {
        return $this->json([]);
    }
}
