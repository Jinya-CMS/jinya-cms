<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 05.03.2018
 * Time: 20:20.
 */

namespace Jinya\Controller\Api\Theme;

use Jinya\Framework\BaseApiController;
use Jinya\Services\Theme\ThemeConfigServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ThemeFormController extends BaseApiController
{
    /**
     * @Route("/api/theme/{name}/form/config", methods={"GET"}, name="api_theme_form_config_get")
     * @IsGranted("ROLE_WRITER")
     *
     * @param string                      $name
     * @param ThemeConfigServiceInterface $themeConfigService
     *
     * @return Response
     */
    public function getConfigFormAction(string $name, ThemeConfigServiceInterface $themeConfigService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($name, $themeConfigService) {
            return $themeConfigService->getConfigForm($name);
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/theme/{name}/form/variables", methods={"GET"}, name="api_theme_form_variables_get")
     * @IsGranted("ROLE_WRITER")
     *
     * @param string                      $name
     * @param ThemeConfigServiceInterface $themeConfigService
     *
     * @return Response
     */
    public function getVariablesFormAction(string $name, ThemeConfigServiceInterface $themeConfigService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($name, $themeConfigService) {
            return $themeConfigService->getVariablesForm($name);
        });

        return $this->json($data, $status);
    }
}
