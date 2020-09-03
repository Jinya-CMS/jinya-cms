<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 05.03.2018
 * Time: 21:04
 */

namespace Jinya\Controller\Api\Configuration;

use Jinya\Formatter\Configuration\ConfigurationFormatterInterface;
use Jinya\Framework\BaseApiController;
use Jinya\Services\Configuration\ConfigurationServiceInterface;
use Jinya\Services\Theme\ThemeServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConfigurationController extends BaseApiController
{
    /**
     * @Route("/api/configuration", methods={"GET"}, name="api_configuration_get")
     * @IsGranted("ROLE_WRITER")
     */
    public function getAction(
        ConfigurationServiceInterface $configurationService,
        ConfigurationFormatterInterface $configurationFormatter
    ): Response {
        [$data, $status] = $this->tryExecute(static function () use ($configurationService, $configurationFormatter) {
            return $configurationFormatter
                ->init($configurationService->getConfig())
                ->theme()
                ->invalidateApiKeyAfter()
                ->messagingCenterEnabled()
                ->format();
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/configuration/{themeName}", methods={"PUT"}, name="api_configuration_frontend_put")
     * @IsGranted("ROLE_WRITER")
     */
    public function putThemeAction(
        string $themeName,
        ConfigurationServiceInterface $configurationService,
        ThemeServiceInterface $themeService
    ): Response {
        [$data, $status] = $this->tryExecute(static function () use ($themeName, $configurationService, $themeService) {
            $configuration = $configurationService->getConfig();
            $configuration->setCurrentTheme($themeService->getTheme($themeName));

            $configurationService->writeConfig($configuration);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }
}
