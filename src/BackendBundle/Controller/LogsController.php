<?php

namespace BackendBundle\Controller;

use HelperBundle\Services\Log\LogServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LogsController extends Controller
{
    /**
     * @Route("/log", name="backend_log_index")
     *
     * @param Request $request
     * @param LogServiceInterface $logService
     * @return Response
     */
    public function indexAction(Request $request): Response
    {
        $logService = $this->get('jinya_gallery.services.log_service');
        $filter = $request->get('filter', '');
        $level = $request->get('level', 'info');
        $sortBy = $request->get('sortBy', 'createdAt');
        $sortOrder = $request->get('sortOrder', 'DESC');
        $offset = $request->get('offset', 0);
        $count = $request->get('count', 20);

        $logs = $logService->getAll($offset, $count, $sortBy, $sortOrder, $level, $filter);

        return $this->render('@Backend/log/index.html.twig', [
            'logs' => $logs
        ]);
    }

    /**
     * @Route("/log/access", name="backend_log_access")
     *
     * @param Request $request
     * @return Response
     */
    public function accessLogAction(Request $request): Response
    {
        $accessLogService = $this->get('jinya_gallery.services.access_log_service');
        $offset = $request->get('offset', 0);
        $count = $request->get('count', 20);

        $logs = $accessLogService->getAll($offset, $count);

        return $this->render('@Backend/log/accessLog.html.twig', [
            'logs' => $logs
        ]);
    }
}
