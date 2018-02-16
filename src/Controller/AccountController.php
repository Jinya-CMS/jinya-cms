<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 26.10.2017
 * Time: 21:13.
 */

namespace Jinya\Controller;

use Jinya\Form\Account\LoginType;
use Jinya\Framework\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AccountController extends BaseController
{
    /**
     * @Route("/account/login", name="jinya_account_login")
     *
     * @param Request $request
     *
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function loginAction(Request $request, AuthenticationUtils $authenticationUtils): Response
    {
        $form = $this->createForm(LoginType::class, []);
        $error = $authenticationUtils->getLastAuthenticationError();

        return $this->render('@Designer/Account/login.html.twig', [
            'form' => $form->createView(),
            'error' => $error
        ]);
    }
}
