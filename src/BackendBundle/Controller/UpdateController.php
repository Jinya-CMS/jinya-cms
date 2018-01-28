<?php

namespace BackendBundle\Controller;

use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UpdateController extends Controller
{
    /**
     * @Route("/update/schema", name="backend_update_schema")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @param Request $request
     * @return Response
     */
    public function schemaAction(Request $request): Response
    {
        try {
            $schemaTool = $this->get('jinya_gallery.components.schema_tool');
            $schemaTool->updateSchema();
            $success = true;
        } catch (Exception $exception) {
            $logger = $this->get('logger');
            $logger->error("Couldn't update database schema");
            $logger->error($exception->getMessage());
            $logger->error($exception->getTraceAsString());
            $success = false;
        }

        return $this->json(['success' => $success], $success ? 200 : 500);
    }
}
