<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 18.02.2018
 * Time: 17:47
 */

namespace Jinya\Controller\Api\Management;

use Jinya\Framework\BaseApiController;
use Jinya\Services\Configuration\ConfigurationServiceInterface;
use Jinya\Services\Theme\ThemeCompilerServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function method_exists;

class AssetsController extends BaseApiController
{
    /**
     * @Route("/api/management/assets/{area}", methods={"POST"}, name="api_management_assets_compile")
     * @IsGranted("ROLE_ADMIN")
     *
     * @param string $area
     * @param ConfigurationServiceInterface $configurationService
     * @param ThemeCompilerServiceInterface $themeCompilerService
     * @return Response
     */
    public function compileAssetsAction(string $area, ConfigurationServiceInterface $configurationService, ThemeCompilerServiceInterface $themeCompilerService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($area, $configurationService, $themeCompilerService) {
            $config = $configurationService->getConfig();
            $method = "getCurrent${area}Theme()";

            if (method_exists($config, $method)) {
                $themeCompilerService->compileTheme($config->{$method});
            }
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }
}