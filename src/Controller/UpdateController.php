<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 18.05.18
 * Time: 23:23
 */

namespace Jinya\Controller;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Jinya\Components\Database\SchemaToolInterface;
use Jinya\Services\Theme\ThemeCompilerServiceInterface;
use Jinya\Services\Theme\ThemeServiceInterface;
use Jinya\Services\Theme\ThemeSyncServiceInterface;
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
    private $currentVersion;

    /** @var string */
    private $kernelProjectDir;

    /** @var Client */
    private $client;

    /** @var CacheClearerInterface */
    private $cacheClearer;

    /** @var ThemeSyncServiceInterface */
    private $themeSyncService;

    /** @var ThemeCompilerServiceInterface */
    private $themeCompilerService;

    /** @var ThemeServiceInterface */
    private $themeService;

    /** @var SchemaToolInterface */
    private $schemaTool;

    /**
     * UpdateController constructor.
     * @param string $currentVersion
     * @param string $kernelProjectDir
     * @param Client $client
     * @param CacheClearerInterface $cacheClearer
     * @param ThemeSyncServiceInterface $themeSyncService
     * @param ThemeCompilerServiceInterface $themeCompilerService
     * @param ThemeServiceInterface $themeService
     * @param SchemaToolInterface $schemaTool
     */
    public function __construct(string $currentVersion, string $kernelProjectDir, Client $client, CacheClearerInterface $cacheClearer, ThemeSyncServiceInterface $themeSyncService, ThemeCompilerServiceInterface $themeCompilerService, ThemeServiceInterface $themeService, SchemaToolInterface $schemaTool)
    {
        $this->currentVersion = $currentVersion;
        $this->kernelProjectDir = $kernelProjectDir;
        $this->client = $client;
        $this->cacheClearer = $cacheClearer;
        $this->themeSyncService = $themeSyncService;
        $this->themeCompilerService = $themeCompilerService;
        $this->themeService = $themeService;
        $this->schemaTool = $schemaTool;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            if ('cancel' === $request->get('action')) {
                $this->finishUpdate();

                return $this->redirectToRoute('designer_home_index');
            } elseif ('start' === $request->get('action')) {
                $this->performFileUpdate($request->get('newVersion'));

                return $this->redirectToRoute('update_database');
            }
        }

        return $this->render('@Jinya/Updater/Default/index.html.twig', [
            'currentVersion' => $this->currentVersion,
        ]);
    }

    private function finishUpdate(): void
    {
        $fs = new Filesystem();
        $fs->remove($this->kernelProjectDir . DIRECTORY_SEPARATOR . 'config/update.lock');
    }

    /**
     * @param string $url
     */
    private function performFileUpdate(string $url)
    {
        $tmpFile = $this->kernelProjectDir . '/var/tmp/update.zip';
        $this->client->get($url, [RequestOptions::SINK => $tmpFile]);
        $backupPath = $this->kernelProjectDir . '/var/tmp/backup/' . time();

        @mkdir($backupPath, 0777, true);
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
            $this->schemaTool->migrateSchema();
            $this->finishUpdate();

            return $this->redirectToRoute('update_done');
        }

        return $this->render('@Jinya/Updater/Default/updateDatabase.html.twig');
    }

    /**
     * @return Response
     */
    public function doneAction(): Response
    {
        return $this->render('@Jinya/Updater/Default/done.html.twig');
    }
}
