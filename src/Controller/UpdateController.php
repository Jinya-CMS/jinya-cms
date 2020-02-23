<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 18.05.18
 * Time: 23:23
 */

namespace Jinya\Controller;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Jinya\Components\Database\DatabaseMigratorInterface;
use Jinya\Services\Theme\ThemeCompilerServiceInterface;
use Jinya\Services\Theme\ThemeServiceInterface;
use Jinya\Services\Theme\ThemeSyncServiceInterface;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\CacheClearer\CacheClearerInterface;
use Throwable;
use ZipArchive;

class UpdateController extends AbstractController
{
    /** @var string */
    private string $currentVersion;

    /** @var string */
    private string $kernelProjectDir;

    /** @var Client */
    private Client $client;

    /** @var CacheClearerInterface */
    private CacheClearerInterface $cacheClearer;

    /** @var ThemeSyncServiceInterface */
    private ThemeSyncServiceInterface $themeSyncService;

    /** @var ThemeCompilerServiceInterface */
    private ThemeCompilerServiceInterface $themeCompilerService;

    /** @var ThemeServiceInterface */
    private ThemeServiceInterface $themeService;

    /** @var DatabaseMigratorInterface */
    private DatabaseMigratorInterface $databaseMigrator;

    /**
     * UpdateController constructor.
     * @param string $currentVersion
     * @param string $kernelProjectDir
     * @param Client $client
     * @param CacheClearerInterface $cacheClearer
     * @param ThemeSyncServiceInterface $themeSyncService
     * @param ThemeCompilerServiceInterface $themeCompilerService
     * @param ThemeServiceInterface $themeService
     * @param DatabaseMigratorInterface $databaseMigrator
     */
    public function __construct(
        string $currentVersion,
        string $kernelProjectDir,
        Client $client,
        CacheClearerInterface $cacheClearer,
        ThemeSyncServiceInterface $themeSyncService,
        ThemeCompilerServiceInterface $themeCompilerService,
        ThemeServiceInterface $themeService,
        DatabaseMigratorInterface $databaseMigrator
    ) {
        $this->currentVersion = $currentVersion;
        $this->kernelProjectDir = $kernelProjectDir;
        $this->client = $client;
        $this->cacheClearer = $cacheClearer;
        $this->themeSyncService = $themeSyncService;
        $this->themeCompilerService = $themeCompilerService;
        $this->themeService = $themeService;
        $this->databaseMigrator = $databaseMigrator;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request): Response
    {
        try {
            if ($request->isMethod('POST')) {
                if ('cancel' === $request->get('action')) {
                    $this->finishUpdate();

                    return $this->redirectToRoute('designer_home_index');
                }

                if ('start' === $request->get('action')) {
                    $this->performFileUpdate($request->get('newVersion'));

                    return $this->redirectToRoute('update_database');
                }
            }

            $response = $this->client->request('GET', 'https://files.jinya.de/stable.json');
            if (Response::HTTP_OK === $response->getStatusCode()) {
                $jsonData = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
                $cmsData = $jsonData['cms'];
                $versions = [];
                foreach ($cmsData as $version => $path) {
                    if (version_compare($version, $this->currentVersion) >= 0) {
                        $versions[] = ['version' => $version, 'path' => $path];
                    }
                }

                if (empty($versions)) {
                    return $this->render(
                        '@Jinya/Updater/Default/no_update_available.html.twig',
                        ['currentVersion' => $this->currentVersion]
                    );
                }

                return $this->render('@Jinya/Updater/Default/index.html.twig', [
                    'currentVersion' => $this->currentVersion,
                    'versions' => array_reverse($versions),
                ]);
            }

            return $this->render('@Jinya/Updater/Default/index.html.twig', [
                'currentVersion' => $this->currentVersion,
                'not_found' => true,
            ]);
        } catch (GuzzleException | Exception $e) {
            return $this->render('@Jinya/Updater/Default/index.html.twig', [
                'currentVersion' => $this->currentVersion,
                'exception' => $e,
            ]);
        }
    }

    private function finishUpdate(): void
    {
        $fs = new Filesystem();
        $fs->remove($this->kernelProjectDir . DIRECTORY_SEPARATOR . 'config/update.lock');
    }

    /**
     * @param string $url
     * @throws Exception
     */
    private function performFileUpdate(string $url): void
    {
        $tmpFile = $this->kernelProjectDir . '/var/tmp/update.zip';
        $this->client->get($url, [RequestOptions::SINK => $tmpFile]);
        $backupPath = $this->kernelProjectDir . '/var/tmp/backup/' . time();

        if (!mkdir($backupPath, 0777, true) && !is_dir($backupPath)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $backupPath));
        }
        @rename($this->kernelProjectDir . '/src', $backupPath);

        try {
            $zipArchive = new ZipArchive();
            $zipArchive->open($tmpFile);
            $zipArchive->extractTo($this->kernelProjectDir);

            $this->cacheClearer->clear($this->kernelProjectDir . '/var/cache');

            $this->themeSyncService->syncThemes();
            $themes = $this->themeService->getAllThemes();
            foreach ($themes as $theme) {
                $this->themeCompilerService->compileTheme($theme);
            }
        } catch (Throwable $exception) {
            rename($backupPath, $this->kernelProjectDir . '/src');
        }
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function updateDatabaseAction(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $this->databaseMigrator->migrate();

            return $this->redirectToRoute('update_done');
        }

        return $this->render('@Jinya/Updater/Default/updateDatabase.html.twig');
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function doneAction(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $this->finishUpdate();

            return $this->redirectToRoute('designer_home_index');
        }

        return $this->render('@Jinya/Updater/Default/done.html.twig');
    }
}
