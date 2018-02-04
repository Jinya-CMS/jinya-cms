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
        return $this->redirectToRoute('backend_accesslog_overview');
    }

    /**
     * @Route("/accesslog/overview", name="backend_accesslog_overview")
     *
     * @param Request $request
     * @return Response
     */
    public function overviewAction(Request $request): Response
    {
        return $this->render('@Backend/accesslog/overview.html.twig');
    }

    /**
     * @Route("/accesslog/details/{id}", name="backend_accesslog_details")
     *
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function detailsAction(int $id, Request $request): Response
    {
        $accessLogService = $this->get('jinya_gallery.services.access_log_service');
        $accessLog = $accessLogService->get($id);
        return $this->render('@Backend/accesslog/details.html.twig', ['entry' => $accessLog]);
    }

    /**
     * @Route("/accesslog/list", name="backend_accesslog_getlist", methods={"POST"})
     *
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getListAction(Request $request): Response
    {
        $accessLogService = $this->get('jinya_gallery.services.access_log_service');
        $datatablesRequestParser = $this->get('jinya_gallery.components.datatables_request_parser');

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
