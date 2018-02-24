<?php

namespace Jinya\Controller\Designer;

use Jinya\Services\Configuration\ConfigurationServiceInterface;
use Jinya\Services\Media\MediaServiceInterface;
use Jinya\Services\Menu\MenuServiceInterface;
use Jinya\Services\Theme\ThemeConfigServiceInterface;
use Jinya\Services\Theme\ThemeServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function array_replace_recursive;
use function preg_match_all;

class ThemeController extends Controller
{
    /**
     * @Route("/designer/theme/", name="designer_theme_index")
     *
     * @param ThemeServiceInterface $themeService
     * @return Response
     */
    public function indexAction(ThemeServiceInterface $themeService): Response
    {
        return $this->render('@Designer/theme/index.html.twig', [
            'themes' => $themeService->getAllThemes()
        ]);
    }

    /**
     * @Route("/designer/theme/{name}", name="designer_theme_config")
     *
     * @param string $name
     * @param Request $request
     * @param ThemeServiceInterface $themeService
     * @param ThemeConfigServiceInterface $themeConfigService
     * @param ConfigurationServiceInterface $configurationService
     * @param MenuServiceInterface $menuService
     * @return Response
     */
    public function configAction(string $name, Request $request, ThemeServiceInterface $themeService, ThemeConfigServiceInterface $themeConfigService, ConfigurationServiceInterface $configurationService, MenuServiceInterface $menuService): Response
    {

        $configuration = $configurationService->getConfig();

        if ($request->isMethod('POST')) {
            $variables = $request->get('scss_variables');
            $themeConfigService->setVariables($name, array_filter($variables));

            if ($request->get('general')['active']['frontend']) {
                $configuration->setCurrentFrontendTheme($themeService->getTheme($name));
            }

            if ($request->get('general')['active']['designer']) {
                $configuration->setCurrentDesignerTheme($themeService->getTheme($name));
            }
            $configurationService->writeConfig($configuration);

            $this->postThemeConfig($name, $request->get('configuration'), $request->files);
            $themeConfigService->setMenus($name, $request->get('menu'));
        }

        $configForm = $themeConfigService->getConfigForm($name);
        $theme = $themeService->getTheme($name);
        $config = $theme->getConfiguration();

        $variables = $themeConfigService->getVariablesForm($name);

        $menus = $menuService->getAll();

        return $this->render('@Designer/theme/config.html.twig', [
            'configForm' => $configForm,
            'config' => $config,
            'theme' => $theme,
            'variables' => $variables,
            'menus' => $menus,
            'frontend' => $configuration
        ]);
    }

    /**
     * @param string $name
     * @param array $configuration
     * @param FileBag $files
     * @param ThemeServiceInterface $themeService
     * @param ThemeConfigServiceInterface $themeConfigService
     * @param MediaServiceInterface $mediaService
     * @return void
     */
    private function postThemeConfig(string $name, array $configuration, FileBag $files, ThemeServiceInterface $themeService, ThemeConfigServiceInterface $themeConfigService, MediaServiceInterface $mediaService): void
    {
        $oldConfiguration = $themeService->getTheme($name)->getConfiguration();

        foreach ($files->get('configuration') as $aKey => $file) {
            foreach ($file as $fKey => $item) {
                if (!is_array($item)) {
                    $item = [$fKey => $item];
                }
                list($result, $key, $uploadedFile) = $this->getKeyAndFile($item);
                if ($result) {
                    $path = $mediaService->saveMedia($uploadedFile, 'themeconfig-' . $name);

                    $temp = &$configuration;
                    $temp = &$temp[$aKey];
                    preg_match_all('/\[(.*?)\]/', $key, $exploded);
                    foreach ($exploded[1] as $elem) {
                        $temp = &$temp[$elem];
                    }
                    $temp = $path;
                    unset($temp);

                    $configuration[$key] = $path;
                }
            }
        }
        $themeConfigService->saveConfig(array_replace_recursive($oldConfiguration, $configuration), $name);
    }

    /**
     * @param array|null $data
     * @param string $key
     * @return array
     */
    private function getKeyAndFile(array $data = null, string $key = ''): array
    {
        if ($data) {
            foreach ($data as $aKey => $elem) {
                $key .= "[$aKey]";

                if ($elem instanceof UploadedFile) {
                    return [true, $key, $elem];
                }

                return $this->getKeyAndFile($elem, $key);
            }
        }

        return [false, null, null];
    }
}
