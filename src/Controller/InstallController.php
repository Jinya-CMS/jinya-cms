<?php

namespace Jinya\Controller;

use Doctrine\DBAL\DBALException;
use Exception;
use Jinya\Components\Database\DatabaseMigratorInterface;
use Jinya\Components\Database\SchemaToolInterface;
use Jinya\Entity\Artist\User;
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
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig_Error_Loader;
use Twig_Error_Runtime;
use Twig_Error_Syntax;

/**
 * Class InstallController
 */
class InstallController extends AbstractController
{
    /** @var SchemaToolInterface */
    private $schemaTool;

    /** @var string */
    private $kernelProjectDir;

    /** @var UserServiceInterface */
    private $userService;

    /** @var Environment */
    private $twig;

    /** @var MediaServiceInterface */
    private $mediaService;

    /** @var ThemeSyncServiceInterface */
    private $themeSyncService;

    /** @var DatabaseMigratorInterface */
    private $databaseMigrator;

    /**
     * InstallController constructor.
     * @param SchemaToolInterface $schemaTool
     * @param string $kernelProjectDir
     * @param UserServiceInterface $userService
     * @param Environment $twig
     * @param MediaServiceInterface $mediaService
     * @param ThemeSyncServiceInterface $themeSyncService
     * @param DatabaseMigratorInterface $databaseMigrator
     */
    public function __construct(SchemaToolInterface $schemaTool, string $kernelProjectDir, UserServiceInterface $userService, Environment $twig, MediaServiceInterface $mediaService, ThemeSyncServiceInterface $themeSyncService, DatabaseMigratorInterface $databaseMigrator)
    {
        $this->schemaTool = $schemaTool;
        $this->kernelProjectDir = $kernelProjectDir;
        $this->userService = $userService;
        $this->twig = $twig;
        $this->mediaService = $mediaService;
        $this->themeSyncService = $themeSyncService;
        $this->databaseMigrator = $databaseMigrator;
    }

    /**
     * @param Request $request
     * @return Response
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     * @throws Exception
     */
    public function indexAction(Request $request): Response
    {
        $form = $this->createForm(SetupType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var SetupData $formData */
            $formData = $form->getData();
            $databaseUrl = sprintf(
                'mysql://%s:%s@%s:%d/%s',
                $formData->getDatabaseUser(),
                $formData->getDatabasePassword(),
                $formData->getDatabaseHost(),
                $formData->getDatabasePort(),
                $formData->getDatabaseName()
            );
            $mailerUrl = sprintf(
                '%s://%s:%s@%s:%d',
                $formData->getMailerTransport(),
                $formData->getMailerUser(),
                $formData->getMailerPassword(),
                $formData->getMailerHost(),
                $formData->getMailerPort()
            );

            $parameters = [
                'databaseUrl' => $databaseUrl,
                'appSecret' => bin2hex(random_bytes(50)),
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
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    private function writeEnv(array $parameters): void
    {
        $fs = new Filesystem();
        $htaccess = $this->twig->load('@Jinya\Installer\Config\.htaccess.twig')->render($parameters);
        $dotenv = $this->twig->load('@Jinya\Installer\Config\.env.twig')->render($parameters);

        $fs->dumpFile($this->kernelProjectDir . '/public/.htaccess', $htaccess);
        $fs->dumpFile($this->kernelProjectDir . '/.env', $dotenv);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function createDatabaseAction(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $this->schemaTool->createSchema();

            try {
                $this->databaseMigrator->activateAllMigrations();
            } catch (DBALException $e) {
                return $this->render('@Jinya\Installer\Default\createDatabase.html.twig', ['exception' => $e]);
            }

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
     * @return Response
     */
    public function createAdminAction(Request $request): Response
    {
        $form = $this->createForm(AdminType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                /** @var AdminData $formData */
                $formData = $form->getData();
                $user = new User();
                $user->setEmail($formData->getEmail());
                $user->setArtistName($formData->getArtistName());
                $user->setPassword($formData->getPassword());

                $user->addRole(User::ROLE_SUPER_ADMIN);
                $user->addRole(User::ROLE_ADMIN);
                $user->addRole(User::ROLE_WRITER);
                $user->setEnabled(true);

                $path = $this->mediaService->saveMedia(
                    $formData->getProfilePicture(),
                    MediaServiceInterface::PROFILE_PICTURE
                );

                $user->setProfilePicture($path);
                $this->userService->saveOrUpdate($user);
                $this->themeSyncService->syncThemes();

                $fs = new Filesystem();
                $fs->touch($this->kernelProjectDir . '/config/install.lock');
            } catch (Exception $exception) {
                return $this->render('@Jinya\Installer\Default\createAdmin.html.twig', [
                    'form' => $form->createView(),
                    'exception' => $exception,
                ]);
            }

            return $this->redirectToRoute('install_done');
        }

        return $this->render('@Jinya\Installer\Default\createAdmin.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
