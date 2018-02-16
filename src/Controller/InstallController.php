<?php

namespace Jinya\Controller;

use Jinya\Components\Database\SchemaToolInterface;
use Jinya\Form\Backend\AddUserData;
use Jinya\Form\Install\AdminData;
use Jinya\Form\Install\AdminType;
use Jinya\Form\Install\SetupData;
use Jinya\Form\Install\SetupType;
use Jinya\Services\Users\UserServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class InstallController
 * @package Jinya\Controller
 */
class InstallController extends AbstractController
{
    /** @var SchemaToolInterface */
    private $schemaTool;
    /** @var string */
    private $kernelProjectDir;
    /** @var UserServiceInterface */
    private $userService;

    /**
     * InstallController constructor.
     * @param SchemaToolInterface $schemaTool
     * @param string $kernelProjectDir
     * @param UserServiceInterface $userService
     */
    public function __construct(SchemaToolInterface $schemaTool, string $kernelProjectDir, UserServiceInterface $userService)
    {
        $this->schemaTool = $schemaTool;
        $this->kernelProjectDir = $kernelProjectDir;
        $this->userService = $userService;
    }

    /**
     * @Route("/install", name="install_default_index")
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request): Response
    {
        $form = $this->createForm(SetupType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var SetupData $formData */
            $formData = $form->getData();
            $databaseUrl = 'mysql://' . $formData->getDatabaseUser() . ':' . $formData->getDatabasePassword() . '@' . $formData->getDatabaseHost() . ':' . $formData->getDatabasePort() . '/' . $formData->getDatabaseName();
            $mailerUrl = $formData->getMailerTransport() . '://' . $formData->getMailerUser() . ':' . $formData->getMailerPassword() . '@' . $formData->getMailerHost() . ':' . $formData->getMailerPort();

            $parameters = [
                'DATABASE_URL' => $databaseUrl,
                'APP_SECRET' => uniqid(),
                'APP_ENV' => $formData->getEnvironment() ?? 'prod',
                'MAILER_URL' => $mailerUrl
            ];

            $this->writeEnv($parameters);
            $this->schemaTool->updateSchema();

            return $this->redirectToRoute('install_admin_create');
        }
        return $this->render('@Jinya_Install/default/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    private function writeEnv(array $parameters)
    {
        $fs = new Filesystem();
        $data = '';

        foreach ($parameters as $key => $parameter) {
            $data .= "\n$key=$parameter";
        }

        $fs->dumpFile($this->kernelProjectDir . '/.env', $data);
    }

    /**
     * @Route("/install/user", name="install_admin_create")
     * @param Request $request
     * @return Response
     */
    public function createAdminAction(Request $request): Response
    {
        $form = $this->createForm(AdminType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var AdminData $formData */
            $formData = $form->getData();
            $addUserData = new AddUserData();
            $addUserData->setProfilePicture($formData->getProfilePicture());
            $addUserData->setEmail($formData->getEmail());
            $addUserData->setLastname($formData->getLastname());
            $addUserData->setFirstname($formData->getFirstname());
            $addUserData->setPassword($formData->getPassword());

            $addUserData->setSuperAdmin(true);
            $addUserData->setAdmin(true);
            $addUserData->setWriter(true);
            $addUserData->setActive(true);

            $this->userService->createUser($addUserData);

            $fs = new Filesystem();
            $fs->touch($this->kernelProjectDir . '/config/install.lock');

            return $this->redirectToRoute('designer_default_index');
        }

        return $this->render('@Jinya_Install/Default/createAdmin.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
