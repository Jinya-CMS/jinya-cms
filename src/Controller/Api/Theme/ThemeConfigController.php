<?php

namespace Jinya\Controller\Api\Theme;

use Jinya\Components\Arrays\ArrayUtilInterface;
use Jinya\Framework\BaseApiController;
use Jinya\Services\Theme\ThemeConfigServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ThemeConfigController extends BaseApiController
{
    /**
     * @Route("/api/theme/{name}/field/{key}", methods={"PUT"}, name="api_theme_put_field")
     * @IsGranted("ROLE_WRITER")
     *
     * @param string $name
     * @param string $key
     * @param ArrayUtilInterface $arrayUtil
     * @param ThemeConfigServiceInterface $themeConfigService
     * @return Response
     */
    public function putFieldAction(
        string $name,
        string $key,
        ArrayUtilInterface $arrayUtil,
        ThemeConfigServiceInterface $themeConfigService
    ): Response {
        [$data, $status] = $this->tryExecute(function () use (
            $arrayUtil,
            $name,
            $key,
            $themeConfigService
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
        [$data, $status] = $this->tryExecute(static function () use ($name, $themeConfigService) {
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
        [$data, $status] = $this->tryExecute(static function () use ($name, $themeConfigService) {
            $themeConfigService->resetVariables($name);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }
}
