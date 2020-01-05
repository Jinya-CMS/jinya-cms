<?php

namespace Jinya\Controller\Api\Maintenance;

use Jinya\Framework\BaseApiController;
use Jinya\Services\Cache\CacheStatusServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CacheStatusController extends BaseApiController
{
    /**
     * @Route("/api/cache", methods={"GET"}, name="api_cache_status_get")
     * @IsGranted("ROLE_SUPER_ADMIN")
     *
     * @param CacheStatusServiceInterface $cacheStatusService
     * @return Response
     */
    public function getCacheAction(CacheStatusServiceInterface $cacheStatusService): Response
    {
        return $this->json([
            'jinya' => $cacheStatusService->getJinyaCacheState(),
            'apcu' => $cacheStatusService->getApcuCacheState(),
            'opcache' => $cacheStatusService->getOpCacheState(),
            'symfony' => $cacheStatusService->getSymfonyCacheState(),
        ]);
    }

    /**
     * @Route("/api/cache/jinya", methods={"DELETE"}, name="api_cache_status_delete_jinya")
     * @IsGranted("ROLE_SUPER_ADMIN")
     *
     * @param CacheStatusServiceInterface $cacheStatusService
     * @return Response
     */
    public function deleteJinyaCacheAction(CacheStatusServiceInterface $cacheStatusService): Response
    {
        [$data, $status] = $this->tryExecute(static function () use ($cacheStatusService) {
            $cacheStatusService->clearJinyaCacheState();
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/cache/symfony", methods={"DELETE"}, name="api_cache_status_delete_symfony")
     * @IsGranted("ROLE_SUPER_ADMIN")
     *
     * @param CacheStatusServiceInterface $cacheStatusService
     * @return Response
     */
    public function deleteSymfonyCacheAction(CacheStatusServiceInterface $cacheStatusService): Response
    {
        [$data, $status] = $this->tryExecute(static function () use ($cacheStatusService) {
            $cacheStatusService->clearSymfonyCache();
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/cache/apcu", methods={"DELETE"}, name="api_cache_status_delete_apcu")
     * @IsGranted("ROLE_SUPER_ADMIN")
     *
     * @param CacheStatusServiceInterface $cacheStatusService
     * @return Response
     */
    public function deleteApcuCacheAction(CacheStatusServiceInterface $cacheStatusService): Response
    {
        [$data, $status] = $this->tryExecute(static function () use ($cacheStatusService) {
            $cacheStatusService->clearApcuCache();
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/cache/opcache", methods={"DELETE"}, name="api_cache_status_delete_opcache")
     * @IsGranted("ROLE_SUPER_ADMIN")
     *
     * @param CacheStatusServiceInterface $cacheStatusService
     * @return Response
     */
    public function deleteOpCacheAction(CacheStatusServiceInterface $cacheStatusService): Response
    {
        [$data, $status] = $this->tryExecute(static function () use ($cacheStatusService) {
            $cacheStatusService->clearOpCache();
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }
}
