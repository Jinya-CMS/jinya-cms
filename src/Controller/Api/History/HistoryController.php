<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 26.02.2018
 * Time: 08:05
 */

namespace Jinya\Controller\Api\History;

use Jinya\Framework\BaseApiController;
use Jinya\Services\History\HistoryServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HistoryController extends BaseApiController
{
    /**
     * @Route("/api/history/{class}/{id}", methods={"GET"}, name="api_history_get_detail")
     * @IsGranted("ROLE_WRITER")
     *
     * @param string $class
     * @param int $id
     * @param HistoryServiceInterface $historyService
     * @return Response
     */
    public function getAction(string $class, int $id, HistoryServiceInterface $historyService): Response
    {
        [$data, $status] = $this->tryExecute(static function () use ($class, $id, $historyService) {
            return $historyService->getHistory($class, $id);
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/history/{class}/{id}/{timestamp}/{field}", methods={"PUT"}, name="api_history_revert")
     * @IsGranted("ROLE_WRITER")
     *
     * @param string $class
     * @param int $id
     * @param string $timestamp
     * @param string $field
     * @param HistoryServiceInterface $historyService
     * @return Response
     */
    public function revertAction(
        string $class,
        int $id,
        string $timestamp,
        string $field,
        HistoryServiceInterface $historyService
    ): Response {
        [$data, $status] = $this->tryExecute(static function () use ($class, $id, $timestamp, $field, $historyService) {
            $historyService->revert($class, $id, $field, $timestamp);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/history/{class}/{id}", methods={"DELETE"}, name="api_history_clear")
     * @IsGranted("ROLE_ADMIN")
     *
     * @param string $class
     * @param int $id
     * @param HistoryServiceInterface $historyService
     * @return Response
     */
    public function clearAction(string $class, int $id, HistoryServiceInterface $historyService): Response
    {
        [$data, $status] = $this->tryExecute(static function () use ($class, $id, $historyService) {
            $historyService->clearHistory($class, $id);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }
}
