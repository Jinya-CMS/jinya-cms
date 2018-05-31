<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 19.05.18
 * Time: 16:19
 */

namespace Jinya\Controller\Api\Maintenance;

use Jinya\Framework\BaseApiController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UpdateController extends BaseApiController
{
    /** @var string */
    private $kernelProjectDir;

    /**
     * @Route("/api/maintenance/update", methods={"POST"}, name="api_maintenance_update")
     * @IsGranted("ROLE_SUPER_ADMIN")
     *
     * @return Response
     */
    public function initiateUpdateAction(): Response
    {
        $code = uniqid();
        $fs = new Filesystem();
        $fs->dumpFile($this->kernelProjectDir . DIRECTORY_SEPARATOR . 'config/update.lock', $code);

        $response = new JsonResponse($this->generateUrl('update_index', [], true));
        $response->headers->setCookie(new Cookie('JinyaUpdateKey', $code));

        return $response;
    }

    /**
     * @param string $kernelProjectDir
     */
    public function setKernelProjectDir(string $kernelProjectDir): void
    {
        $this->kernelProjectDir = $kernelProjectDir;
    }
}