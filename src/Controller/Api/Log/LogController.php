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
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LogController extends BaseApiController
{
    /**
     * @Route("/api/log", methods={"GET"}, name="api_log_get_all")
     * @IsGranted("ROLE_ADMIN", statusCode=403)
     *
     * @param Request $request
     * @param LogServiceInterface $logService
     * @param UrlGeneratorInterface $urlGenerator
     * @return Response
     */
    public function getAllAction(
        Request $request,
        LogServiceInterface $logService,
        UrlGeneratorInterface $urlGenerator
    ): Response {
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
                'level' => $level,
            ];

            return $this->formatListResult($entityCount, $offset, $count, $parameter, 'api_log_get_all', $entities);
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/log/{id}", methods={"GET"}, name="api_log_get")
     * @IsGranted("ROLE_ADMIN", statusCode=403)
     *
     * @param int $id
     * @param LogServiceInterface $logService
     * @return Response
     */
    public function getAction(int $id, LogServiceInterface $logService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($id, $logService) {
            return $logService->get($id);
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/log", methods={"POST"}, name="api_log_post")
     * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
     *
     * @param Request $request
     * @param LoggerInterface $logger
     * @return Response
     */
    public function postAction(Request $request, LoggerInterface $logger): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($request, $logger) {
            $level = Logger::toMonologLevel($this->getValue('level', 'INFO'));
            $message = $this->getValue('message', null);
            $context = $request->attributes->all();
            $context['referer'] = $request->server->get('HTTP_REFERER');

            $logger->log($level, $message, $context);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/log", methods={"DELETE"}, name="api_log_delete")
     * @IsGranted("ROLE_SUPER_ADMIN", statusCode=403)
     *
     * @param LogServiceInterface $logService
     * @return Response
     */
    public function deleteAction(LogServiceInterface $logService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($logService) {
            $logService->clear();
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }
}
