<?php

namespace Jinya\Controller\Backend;

use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AssetController extends Controller
{
    /**
     * @Route("/asset/dump", name="backend_asset_dump")
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @return Response
     */
    public function dumpAction(): Response
    {
        try {
            $success = true;
            $assetDumper = $this->get('jinya_gallery.components.asset_dumper');
            $assetDumper->dumpAssets();
        } catch (Exception $exception) {
            $logger = $this->get('logger');
            $logger->error('Asset dump failed');
            $logger->error($exception->getMessage());
            $logger->error($exception->getTraceAsString());
            $success = false;
        }

        return $this->json([
            'success' => $success
        ], $success ? 200 : 500);
    }
}
