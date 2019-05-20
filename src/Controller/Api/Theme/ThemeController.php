<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 05.03.2018
 * Time: 07:54
 */

namespace Jinya\Controller\Api\Theme;

use Jinya\Components\Arrays\ArrayUtilInterface;
use Jinya\Formatter\Theme\ThemeFormatterInterface;
use Jinya\Framework\BaseApiController;
use Jinya\Services\Media\MediaServiceInterface;
use Jinya\Services\Theme\ThemeConfigServiceInterface;
use Jinya\Services\Theme\ThemeServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function array_key_exists;
use function array_map;
use function count;

class ThemeController extends BaseApiController
{
    /**
     * @Route("/api/theme", methods={"GET"}, name="api_theme_get_all")
     * @IsGranted("ROLE_WRITER")
     *
     * @param ThemeServiceInterface $themeService
     * @param ThemeFormatterInterface $themeFormatter
     * @return Response
     */
    public function getAllAction(ThemeServiceInterface $themeService, ThemeFormatterInterface $themeFormatter): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($themeFormatter, $themeService) {
            $themes = array_map(function ($item) use ($themeFormatter) {
                return $themeFormatter
                    ->init($item)
                    ->name()
                    ->description()
                    ->displayName()
                    ->previewImage()
                    ->format();
            }, $themeService->getAllThemes());

            return $this->formatListResult(count($themes), 0, count($themes), [], 'api_theme_get_all', $themes);
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/theme/{name}", methods={"GET"}, name="api_theme_get")
     * @IsGranted("ROLE_WRITER")
     *
     * @param string $name
     * @param ThemeServiceInterface $themeService
     * @param ThemeFormatterInterface $themeFormatter
     * @return Response
     */
    public function getAction(
        string $name,
        ThemeServiceInterface $themeService,
        ThemeFormatterInterface $themeFormatter
    ): Response {
        list($data, $status) = $this->tryExecute(function () use ($name, $themeFormatter, $themeService) {
            $item = $themeService->getTheme($name);

            return $themeFormatter
                ->init($item)
                ->name()
                ->description()
                ->displayName()
                ->previewImage()
                ->config()
                ->scssVariables()
                ->primaryMenu()
                ->secondaryMenu()
                ->footerMenu()
                ->format();
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/theme/{name}", methods={"PUT"}, name="api_theme_put")
     * @IsGranted("ROLE_WRITER")
     *
     * @param string $name
     * @param ThemeConfigServiceInterface $themeConfigService
     * @return Response
     */
    public function putAction(string $name, ThemeConfigServiceInterface $themeConfigService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($name, $themeConfigService) {
            $config = $this->getValue('config');
            $scss = $this->getValue('scss');
            $menus = $this->getValue('menus');

            if (!empty($menus)) {
                if (array_key_exists('primary', $menus)) {
                    if ($menus['primary']['id'] !== null && $menus['primary']['id'] !== '') {
                        /** @noinspection PhpParamsInspection */
                        $menu = (int) $menus['primary']['id'];
                    } else {
                        $menu = 'unset';
                    }

                    $themeConfigService->setMenus($name, ['primary' => $menu]);
                }
                if (array_key_exists('secondary', $menus)) {
                    if ($menus['secondary']['id'] !== null && $menus['secondary']['id'] !== '') {
                        /** @noinspection PhpParamsInspection */
                        $menu = (int) $menus['secondary']['id'];
                    } else {
                        $menu = 'unset';
                    }

                    $themeConfigService->setMenus($name, ['secondary' => $menu]);
                }
                if (array_key_exists('footer', $menus)) {
                    if ($menus['footer']['id'] !== null && $menus['footer']['id'] !== '') {
                        /** @noinspection PhpParamsInspection */
                        $menu = (int) $menus['footer']['id'];
                    } else {
                        $menu = 'unset';
                    }

                    $themeConfigService->setMenus($name, ['footer' => $menu]);
                }
            }

            if (!empty($config)) {
                $themeConfigService->saveConfig($name, $config);
            }

            if (!empty($scss)) {
                $themeConfigService->setVariables($name, $scss);
            }
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/theme/{name}/file/{key}", methods={"DELETE"}, name="api_theme_delete_file")
     * @IsGranted("ROLE_WRITER")
     *
     * @param string $name
     * @param string $key
     * @param ThemeConfigServiceInterface $themeConfigService
     * @return Response
     */
    public function deleteFileAction(
        string $name,
        string $key,
        ThemeConfigServiceInterface $themeConfigService
    ): Response {
        list($data, $status) = $this->tryExecute(function () use ($name, $key, $themeConfigService) {
            $themeConfigService->removeFile($name, $key);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/theme/{name}/file/{key}", methods={"PUT"}, name="api_theme_put_file")
     * @IsGranted("ROLE_WRITER")
     *
     * @param string $name
     * @param string $key
     * @param Request $request
     * @param ArrayUtilInterface $arrayUtil
     * @param ThemeConfigServiceInterface $themeConfigService
     * @param MediaServiceInterface $mediaService
     * @return Response
     */
    public function putFileAction(
        string $name,
        string $key,
        Request $request,
        ArrayUtilInterface $arrayUtil,
        ThemeConfigServiceInterface $themeConfigService,
        MediaServiceInterface $mediaService
    ): Response {
        list($data, $status) = $this->tryExecute(function () use (
            $arrayUtil,
            $name,
            $key,
            $request,
            $themeConfigService,
            $mediaService
        ) {
            $content = $request->getContent(true);
            $path = $mediaService->saveMedia($content, "themeconfig-$name");
            $data = $arrayUtil->buildArrayFromPath($key);

            $data = $arrayUtil->setArrayValueByPath($data, $key, $path);

            $themeConfigService->saveConfig($name, $data, false);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/theme/{name}/field/{key}", methods={"PUT"}, name="api_theme_put_field")
     * @IsGranted("ROLE_WRITER")
     *
     * @param string $name
     * @param string $key
     * @param ArrayUtilInterface $arrayUtil
     * @param ThemeConfigServiceInterface $themeConfigService
     * @param MediaServiceInterface $mediaService
     * @return Response
     */
    public function putFieldAction(
        string $name,
        string $key,
        ArrayUtilInterface $arrayUtil,
        ThemeConfigServiceInterface $themeConfigService,
        MediaServiceInterface $mediaService
    ): Response {
        list($data, $status) = $this->tryExecute(function () use (
            $arrayUtil,
            $name,
            $key,
            $themeConfigService,
            $mediaService
        ) {
            $content = $this->getValue('value');
            $data = $arrayUtil->buildArrayFromPath($key);

            $arrayUtil->setArrayValueByPath($data, $key, $content);

            $themeConfigService->saveConfig($name, $data, false);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/theme/{name}/config", methods={"DELETE"}, name="api_theme_delete_config")
     * @IsGranted("ROLE_WRITER")
     *
     * @param string $name
     * @param ThemeConfigServiceInterface $themeConfigService
     * @return Response
     */
    public function deleteConfigAction(string $name, ThemeConfigServiceInterface $themeConfigService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($name, $themeConfigService) {
            $themeConfigService->resetConfig($name);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/theme/{name}/variables", methods={"DELETE"}, name="api_theme_delete_variables")
     * @IsGranted("ROLE_WRITER")
     *
     * @param string $name
     * @param ThemeConfigServiceInterface $themeConfigService
     * @return Response
     */
    public function deleteScssVariablesAction(string $name, ThemeConfigServiceInterface $themeConfigService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($name, $themeConfigService) {
            $themeConfigService->resetVariables($name);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }
}
