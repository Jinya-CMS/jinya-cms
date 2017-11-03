<?php

namespace BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccessLogsController extends Controller
{
    /**
     * @Route("/accesslog", name="backend_accesslog_index")
     *
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request): Response
    {
        return $this->render('@Backend/accesslog/index.html.twig');
    }

    /**
     * @Route("/accesslog/list", name="backend_accesslog_getlist", methods={"POST"})
     *
     * @param Request $request
     * @return Response
     */
    public function getListAction(Request $request): Response
    {
        $accessLogService = $this->get('jinya_gallery.services.access_log_service');
        $sortBy = 'createdAt';
        $sortOrder = 'desc';
        if (!empty($request->get('order'))) {
            $order = $request->get('order')[0];
            $sortOrder = $order['dir'];
            $sortBy = $this->mapColumnIdToName($order['column']);
        }
        $items = $accessLogService->getAll($request->get('start'), $request->get('length'), $sortBy, $sortOrder);
        $count = $accessLogService->countAll();
        return $this->json([
            'draw' => uniqid('', true),
            'recordsTotal' => $count,
            'recordsFiltered' => $count,
            'data' => $items
        ]);
    }

    private function mapColumnIdToName($id)
    {
        $columns = [
            0 => 'clientIp',
            1 => 'uri',
            2 => 'method',
            3 => 'createdAt'
        ];
        return $columns[(int)$id];
    }
}
