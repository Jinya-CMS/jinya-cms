<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 18.02.2018
 * Time: 18:00
 */

namespace Jinya\Controller\Api\Log;

use Jinya\Framework\BaseApiController;
use Jinya\Services\Log\LogServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LogController extends BaseApiController
{
    /**
     * @Route("/api/log", methods={"GET"}, name="api_log_get_all")
     *
     * @param Request $request
     * @param LogServiceInterface $logService
     * @param UrlGeneratorInterface $urlGenerator
     * @return Response
     */
    public function getAllAction(Request $request, LogServiceInterface $logService, UrlGeneratorInterface $urlGenerator): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($request, $logService, $urlGenerator) {
            $offset = $request->get('offset', 0);
            $count = $request->get('count', 20);
            $sortBy = $request->get('sortBy', 'createdAt');
            $sortOrder = $request->get('sortOrder', 'desc');
            $level = $request->get('level', 'info');
            $filter = $request->get('filter', '');

            $entityCount = $logService->countFiltered($level, $filter);
            $entities = $logService->getAll($offset, $count, $sortBy, $sortOrder, $level, $filter);
            $parameter = [
                'offset' => $offset,
                'count' => $count,
                'filter' => $filter,
                'sortBy' => $sortBy,
                'sortOrder' => $sortOrder,
                'level' => $level
            ];

            return $this->formatListResult($entityCount, $offset, $count, $parameter, 'api_log_get_all', $entities);
        });

        return $this->json($data, $status);
    }
}