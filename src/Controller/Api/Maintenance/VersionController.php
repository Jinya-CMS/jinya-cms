<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 19.05.18
 * Time: 16:59
 */

namespace Jinya\Controller\Api\Maintenance;

use Jinya\Framework\BaseApiController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VersionController extends BaseApiController
{
    private string $jinyaVersion;

    /**
     * @Route("/api/maintenance/version", methods={"GET"}, name="api_maintenance_version")
     * @IsGranted("ROLE_WRITER")
     */
    public function getVersionAction(): Response
    {
        return $this->json($this->jinyaVersion);
    }

    public function setJinyaVersion(string $jinyaVersion): void
    {
        $this->jinyaVersion = $jinyaVersion;
    }
}
