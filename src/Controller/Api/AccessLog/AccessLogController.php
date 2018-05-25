<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 18.02.2018
 * Time: 18:00.
 */

namespace Jinya\Controller\Api\AccessLog;

use Jinya\Framework\BaseApiController;
use Jinya\Services\Log\AccessLogServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AccessLogController extends BaseApiController
{
    /**
     * @Route("/api/accesslog", methods={"GET"}, name="api_accessLog_get_all")
     * @IsGranted("ROLE_ADMIN", statusCode=403)
     *
     * @param Request                   $request
     * @param AccessLogServiceInterface $accessLogService
     * @param UrlGeneratorInterface     $urlGenerator
     *
     * @return Response
     */
    public function getAllAction(Request $request, AccessLogServiceInterface $accessLogService, UrlGeneratorInterface $urlGenerator): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($request, $accessLogService, $urlGenerator) {
            $offset = $request->get('offset', 0);
            $count = $request->get('count', 20);
            $sortBy = $request->get('sortBy', 'createdAt');
            $sortOrder = $request->get('sortOrder', 'desc');

            $entityCount = $accessLogService->countAll();
            $entities = $accessLogService->getAll($offset, $count, $sortBy, $sortOrder);
            $parameter = [
                'offset' => $offset,
                'count' => $count,
                'sortBy' => $sortBy,
                'sortOrder' => $sortOrder,
            ];

            return $this->formatListResult($entityCount, $offset, $count, $parameter, 'api_accessLog_get_all', $entities);
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/accesslog/{id}", methods={"GET"}, name="api_accessLog_get")
     * @IsGranted("ROLE_ADMIN", statusCode=403)
     *
     * @param int                       $id
     * @param AccessLogServiceInterface $accessLogService
     *
     * @return Response
     */
    public function getAction(int $id, AccessLogServiceInterface $accessLogService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($id, $accessLogService) {
            return $accessLogService->get($id);
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/accesslog", methods={"DELETE"}, name="api_accessLog_delete")
     * @IsGranted("ROLE_SUPER_ADMIN", statusCode=403)
     *
     * @param AccessLogServiceInterface $accessLogService
     *
     * @return Response
     */
    public function deleteAction(AccessLogServiceInterface $accessLogService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($accessLogService) {
            $accessLogService->clear();
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }
}
