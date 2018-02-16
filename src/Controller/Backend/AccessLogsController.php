<?php

namespace Jinya\Controller\Backend;

use Jinya\Components\DataTables\DatatablesRequestParserInterface;
use Jinya\Services\Log\AccessLogServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccessLogsController extends Controller
{
    /**
     * @Route("/accesslog", name="backend_accesslog_index")
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        return $this->redirectToRoute('backend_accesslog_overview');
    }

    /**
     * @Route("/accesslog/overview", name="backend_accesslog_overview")
     *
     * @return Response
     */
    public function overviewAction(): Response
    {
        return $this->render('@Backend/accesslog/overview.html.twig');
    }

    /**
     * @Route("/accesslog/details/{id}", name="backend_accesslog_details")
     *
     * @param int $id
     * @param AccessLogServiceInterface $accessLogService
     * @return Response
     */
    public function detailsAction(int $id, AccessLogServiceInterface $accessLogService): Response
    {
        $accessLog = $accessLogService->get($id);
        return $this->render('@Backend/accesslog/details.html.twig', ['entry' => $accessLog]);
    }

    /**
     * @Route("/accesslog/list", name="backend_accesslog_getlist", methods={"POST"})
     *
     * @param Request $request
     * @param AccessLogServiceInterface $accessLogService
     * @return Response
     */
    public function getListAction(Request $request, AccessLogServiceInterface $accessLogService, DatatablesRequestParserInterface $datatablesRequestParser): Response
    {
        $datatablesRequest = $datatablesRequestParser->parseRequest($request);
        $orders = $datatablesRequest->getOrder();
        $order = array_shift($orders);

        $items = $accessLogService->getAll($datatablesRequest->getStart(), $datatablesRequest->getLength(), $order->getColumn(), $order->getDir());
        $count = $accessLogService->countAll();
        return $this->json([
            'draw' => uniqid('', true),
            'recordsTotal' => $count,
            'recordsFiltered' => $count,
            'data' => $items
        ]);
    }
}
