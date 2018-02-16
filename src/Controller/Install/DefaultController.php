<?php

namespace Jinya\Controller\Install;

use Jinya\Components\Install\ParameterProcessor;
use Jinya\Form\Backend\AddUserData;
use Jinya\Form\Install\AdminData;
use Jinya\Form\Install\AdminType;
use Jinya\Form\Install\SetupData;
use Jinya\Form\Install\SetupType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{

    /**
     * @Route("/", name="install_default_index")
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
            $parameters = [
                'parameters' => [
                    'database_host' => $formData->getDatabaseHost(),
                    'database_port' => $formData->getDatabasePort(),
                    'database_name' => $formData->getDatabaseName(),
                    'database_user' => $formData->getDatabaseUser(),
                    'database_password' => $formData->getDatabasePassword(),
                    'secret' => uniqid(),
                    'mailer_transport' => $formData->getMailerTransport(),
                    'mailer_host' => $formData->getMailerHost(),
                    'mailer_port' => $formData->getMailerPort(),
                    'mailer_user' => $formData->getMailerUser(),
                    'mailer_password' => $formData->getMailerPassword()
                ]
            ];
            ParameterProcessor::processParameters($parameters, $this->getParameter('kernel.root_dir'));

            $schemaTool = $this->get('jinya_gallery.components.schema_tool');
            $schemaTool->updateSchema();

            return $this->redirectToRoute('install_admin_create');
        }
        return $this->render('@Jinya_Install/default/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/user", name="install_admin_create")
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
            $addUserData->setUsername($formData->getUsername());
            $addUserData->setEmail($formData->getEmail());
            $addUserData->setLastname($formData->getLastname());
            $addUserData->setFirstname($formData->getFirstname());
            $addUserData->setPassword($formData->getPassword());

            $addUserData->setSuperAdmin(true);
            $addUserData->setAdmin(true);
            $addUserData->setWriter(true);
            $addUserData->setActive(true);

            $userService = $this->get('jinya_gallery.services.user_service');
            $userService->createUser($addUserData);

            $fs = new Filesystem();
            $fs->touch($this->getParameter('kernel.root_dir') . '/config/install.lock');

            $assetDumper = $this->get('jinya_gallery.components.asset_dumper');
            $assetDumper->dumpAssets();

            return $this->redirectToRoute('backend_default_index');
        }

        return $this->render('@Install/Default/createAdmin.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
