<?php

namespace Jinya\Controller;

use Jinya\Components\Database\SchemaToolInterface;
use Jinya\Entity\User;
use Jinya\Form\Install\AdminData;
use Jinya\Form\Install\AdminType;
use Jinya\Form\Install\SetupData;
use Jinya\Form\Install\SetupType;
use Jinya\Services\Media\MediaServiceInterface;
use Jinya\Services\Theme\ThemeSyncServiceInterface;
use Jinya\Services\Users\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class InstallController.
 */
class InstallController extends AbstractController
{
    /** @var SchemaToolInterface */
    private $schemaTool;
    /** @var string */
    private $kernelProjectDir;
    /** @var UserServiceInterface */
    private $userService;
    /** @var \Twig_Environment */
    private $twig;
    /** @var MediaServiceInterface */
    private $mediaService;
    /** @var ThemeSyncServiceInterface */
    private $themeSyncService;

    /**
     * InstallController constructor.
     *
     * @param SchemaToolInterface   $schemaTool
     * @param string                $kernelProjectDir
     * @param UserServiceInterface  $userService
     * @param \Twig_Environment     $twig
     * @param MediaServiceInterface $mediaService
     */
    public function __construct(SchemaToolInterface $schemaTool, string $kernelProjectDir, UserServiceInterface $userService, \Twig_Environment $twig, MediaServiceInterface $mediaService)
    {
        $this->schemaTool = $schemaTool;
        $this->kernelProjectDir = $kernelProjectDir;
        $this->userService = $userService;
        $this->twig = $twig;
        $this->mediaService = $mediaService;
    }

    /**
     * @param Request $request
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     *
     * @return Response
     */
    public function indexAction(Request $request): Response
    {
        $form = $this->createForm(SetupType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var SetupData $formData */
            $formData = $form->getData();
            $databaseUrl = 'mysql://'.$formData->getDatabaseUser().':'.$formData->getDatabasePassword().'@'.$formData->getDatabaseHost().':'.$formData->getDatabasePort().'/'.$formData->getDatabaseName();
            $mailerUrl = $formData->getMailerTransport().'://'.$formData->getMailerUser().':'.$formData->getMailerPassword().'@'.$formData->getMailerHost().':'.$formData->getMailerPort();

            $parameters = [
                'databaseUrl' => $databaseUrl,
                'appSecret' => uniqid(),
                'appEnv' => 'prod',
                'mailerUrl' => $mailerUrl,
                'mailerSender' => $formData->getMailerSender(),
            ];

            $this->writeEnv($parameters);

            return $this->redirectToRoute('install_database');
        }

        return $this->render('@Jinya\Installer\Default\index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param array $parameters
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    private function writeEnv(array $parameters)
    {
        $fs = new Filesystem();
        $data = $this->twig->load('@Jinya\Installer\Config\.htaccess.twig')->render($parameters);

        $fs->dumpFile($this->kernelProjectDir.'/public/.htaccess', $data);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function createDatabaseAction(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $this->schemaTool->updateSchema();

            return $this->redirectToRoute('install_admin');
        }

        return $this->render('@Jinya\Installer\Default\createDatabase.html.twig');
    }

    /**
     * @return Response
     */
    public function doneAction(): Response
    {
        return $this->render('@Jinya\Installer\Default\done.html.twig');
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function createAdminAction(Request $request): Response
    {
        $form = $this->createForm(AdminType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var AdminData $formData */
            $formData = $form->getData();
            $user = new User();
            $user->setEmail($formData->getEmail());
            $user->setLastname($formData->getLastname());
            $user->setFirstname($formData->getFirstname());
            $user->setPassword($formData->getPassword());

            $user->addRole(User::ROLE_SUPER_ADMIN);
            $user->addRole(User::ROLE_ADMIN);
            $user->addRole(User::ROLE_WRITER);
            $user->setEnabled(true);

            $path = $this->mediaService->saveMedia($formData->getProfilePicture(), MediaServiceInterface::PROFILE_PICTURE);

            $user->setProfilePicture($path);
            $this->userService->saveOrUpdate($user);
            $this->themeSyncService->syncThemes();

            $fs = new Filesystem();
            $fs->touch($this->kernelProjectDir.'/config/install.lock');

            return $this->redirectToRoute('install_done');
        }

        return $this->render('@Jinya\Installer\Default\createAdmin.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
