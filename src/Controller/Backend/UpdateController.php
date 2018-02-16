<?php

namespace Jinya\Controller\Backend;

use Exception;
use Jinya\Components\Database\SchemaToolInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UpdateController extends Controller
{
    /**
     * @Route("/update/schema", name="backend_update_schema")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @param SchemaToolInterface $schemaTool
     * @param LoggerInterface $logger
     * @return Response
     */
    public function schemaAction(SchemaToolInterface $schemaTool, LoggerInterface $logger): Response
    {
        try {
            $schemaTool->updateSchema();
            $success = true;
        } catch (Exception $exception) {
            $logger->error("Couldn't update database schema");
            $logger->error($exception->getMessage());
            $logger->error($exception->getTraceAsString());
            $success = false;
        }

        return $this->json(['success' => $success], $success ? 200 : 500);
    }
}
