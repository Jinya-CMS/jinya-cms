<?php

namespace BackendBundle\Controller;

use HelperBundle\Components\DatatablesRequestParserInterface;
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
     * @return Response
     */
    public function indexAction(Request $request): Response
    {
        return $this->redirectToRoute('backend_log_overview');
    }

    /**
     * @Route("/log/overview", name="backend_log_overview")
     *
     * @param Request $request
     * @return Response
     */
    public function overviewAction(Request $request): Response
    {
        return $this->render('@Backend/log/overview.html.twig');
    }

    /**
     * @Route("/log/list/", name="backend_log_getlist", methods={"POST"})
     *
     * @param Request $request
     * @return Response
     */
    public function getListAction(Request $request): Response
    {
        $logService = $this->get('jinya_gallery.services.log_service');
        $requestParser = $this->get('jinya_gallery.components.datatables_request_parser');
        $datatables = $requestParser->parseRequest($request);
        $sortBy = $datatables->getOrder()[0]->getColumn();
        $sortOrder = $datatables->getOrder()[0]->getDir();
        $levelColumn = $datatables->getColumn('level');

        $items = $logService->getAll($datatables->getStart(), $datatables->getLength(), $sortBy, $sortOrder, $levelColumn->getSearch()->getValue(), $datatables->getSearch()->getValue());
        $count = $logService->countAll();
        $filteredCount = $logService->countFiltered($levelColumn->getSearch()->getValue(), $datatables->getSearch()->getValue());

        return $this->json([
            'draw' => uniqid('', true),
            'recordsTotal' => $count,
            'recordsFiltered' => $filteredCount,
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
