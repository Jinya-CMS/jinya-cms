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
     * @Route("/api/history/{class}/{id}", methods={"GET"})
     * @IsGranted("ROLE_WRITER")
     *
     * @param string $class
     * @param int $id
     * @param HistoryServiceInterface $historyService
     * @return Response
     */
    public function get(string $class, int $id, HistoryServiceInterface $historyService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($class, $id, $historyService) {
            return $historyService->getHistory($class, $id);
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/history/{class}/{id}", methods={"PUT"})
     * @IsGranted("ROLE_WRITER")
     *
     * @param string $class
     * @param int $id
     * @param HistoryServiceInterface $historyService
     * @return Response
     */
    public function revert(string $class, int $id, HistoryServiceInterface $historyService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($class, $id, $historyService) {
            $field = $this->getValue('field');
            $value = $this->getValue('value');

            $historyService->revert($class, $id, $field, $value);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/history/{class}/{id}", methods={"DELETE"})
     * @IsGranted("ROLE_ADMIN")
     *
     * @param string $class
     * @param int $id
     * @param HistoryServiceInterface $historyService
     * @return Response
     */
    public function clear(string $class, int $id, HistoryServiceInterface $historyService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($class, $id, $historyService) {
            $historyService->clearHistory($class, $id);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }
}