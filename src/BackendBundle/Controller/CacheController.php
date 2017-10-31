<?php

namespace BackendBundle\Controller;

use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class CacheController extends Controller
{

    /**
     * @Route("/cache/clear", name="backend_cache_clear")
     * @Security("has_role('ROLE_ADMIN')")
     * @return Response
     */
    public function clearAction(): Response
    {
        try {
            $cacheClearer = $this->get('cache_clearer');
            $cacheDir = $this->getParameter('kernel.cache_dir');
            $cacheClearer->clear($cacheDir);
            $success = true;
        } catch (Exception $exception) {
            $logger = $this->get('logger');
            $logger->error('Cache clearing failed');
            $logger->error($exception->getMessage());
            $logger->error($exception->getTraceAsString());
            $success = false;
        }

        return $this->json([
            'success' => $success
        ], $success ? 200 : 500);
    }

    /**
     * @Route("/cache/warmup", name="backend_cache_warmup")
     * @Security("has_role('ROLE_ADMIN')")
     * @return Response
     */
    public function warmupAction(): Response
    {
        try {
            $cacheWarmer = $this->get('cache_warmer');
            $cacheDir = $this->getParameter('kernel.cache_dir');
            $cacheWarmer->warmUp($cacheDir);
            $success = true;
        } catch (Exception $exception) {
            $logger = $this->get('logger');
            $logger->error('Cache warmup failed');
            $logger->error($exception->getMessage());
            $logger->error($exception->getTraceAsString());
            $success = false;
        }

        return $this->json([
            'success' => $success
        ], $success ? 200 : 500);
    }
}
