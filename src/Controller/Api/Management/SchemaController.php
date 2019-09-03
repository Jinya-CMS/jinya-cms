<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 18.02.2018
 * Time: 17:44
 */

namespace Jinya\Controller\Api\Management;

use Jinya\Components\Database\SchemaToolInterface;
use Jinya\Framework\BaseApiController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SchemaController extends BaseApiController
{
    /**
     * @Route("/api/management/schema", methods={"POST"}, name="api_management_schema_update")
     * @IsGranted("ROLE_SUPER_ADMIN", statusCode=403)
     *
     * @param SchemaToolInterface $schemaTool
     * @return Response
     */
    public function updateSchemaAction(SchemaToolInterface $schemaTool): Response
    {
        [$data, $status] = $this->tryExecute(static function () use ($schemaTool) {
            $schemaTool->migrateSchema();
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }
}
