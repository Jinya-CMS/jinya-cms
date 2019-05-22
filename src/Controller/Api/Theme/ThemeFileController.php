<?php

namespace Jinya\Controller\Api\Theme;

use Jinya\Components\Arrays\ArrayUtilInterface;
use Jinya\Framework\BaseApiController;
use Jinya\Services\Media\MediaServiceInterface;
use Jinya\Services\Theme\ThemeConfigServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ThemeFileController extends BaseApiController
{
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
        [$data, $status] = $this->tryExecute(static function () use ($name, $key, $themeConfigService) {
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
        [$data, $status] = $this->tryExecute(static function () use (
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
}
