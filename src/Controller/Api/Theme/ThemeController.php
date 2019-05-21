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
        [$data, $status] = $this->tryExecute(function () use ($themeFormatter, $themeService) {
            $themes = array_map(static function ($item) use ($themeFormatter) {
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
        [$data, $status] = $this->tryExecute(static function () use ($name, $themeFormatter, $themeService) {
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
        [$data, $status] = $this->tryExecute(function () use ($name, $themeConfigService) {
            $config = $this->getValue('config');
            $scss = $this->getValue('scss');

            if (!empty($config)) {
                $themeConfigService->saveConfig($name, $config);
            }

            if (!empty($scss)) {
                $themeConfigService->setVariables($name, $scss);
            }
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }
}
