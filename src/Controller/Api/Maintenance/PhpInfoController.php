<?php

namespace Jinya\Controller\Api\Maintenance;

use Jinya\Framework\BaseApiController;
use Jinya\Services\PhpInfo\PhpInfoServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PhpInfoController extends BaseApiController
{
    /**
     * @Route("/api/phpinfo", name="api_phpinfo_get", methods={"GET"})
     * @IsGranted("ROLE_SUPER_ADMIN")
     *
     * @param PhpInfoServiceInterface $phpInfoService
     * @return Response
     */
    public function getAction(PhpInfoServiceInterface $phpInfoService): Response
    {
        return $this->json([
            'apache' => [
                'modules' => $phpInfoService->getApacheModules(),
                'version' => $phpInfoService->getApacheVersion(),
            ],
            'system' => [
                'uname' => $phpInfoService->getUname(),
            ],
            'php' => [
                'version' => $phpInfoService->getVersion(),
                'extensions' => $phpInfoService->getLoadedExtensions(),
                'iniValues' => $phpInfoService->getIniValues(),
            ],
            'zend' => [
                'version' => $phpInfoService->getZendVersion(),
            ],
        ]);
    }
}
