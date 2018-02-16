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
use Jinya\Services\Configuration\ConfigurationServiceInterface;
use Jinya\Services\Theme\ThemeCompilerServiceInterface;
use Jinya\Services\Theme\ThemeConfigServiceInterface;
use Jinya\Services\Theme\ThemeServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AccountController extends BaseController
{
    /**
     * AccountController constructor.
     * @param ThemeConfigServiceInterface $themeConfigService
     * @param ThemeServiceInterface $themeService
     * @param ConfigurationServiceInterface $configurationService
     * @param ThemeCompilerServiceInterface $themeCompilerService
     */
    public function __construct(ThemeConfigServiceInterface $themeConfigService, ThemeServiceInterface $themeService, ConfigurationServiceInterface $configurationService, ThemeCompilerServiceInterface $themeCompilerService)
    {
        parent::__construct($themeConfigService, $themeService, $configurationService, $themeCompilerService);
    }

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
