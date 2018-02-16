<?php

namespace Jinya\Controller\Backend;

use Jinya\Components\DataTables\DatatablesRequestParserInterface;
use Jinya\Services\Log\LogServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LogsController extends Controller
{
    /**
     * @Route("/log", name="backend_log_index")
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        return $this->redirectToRoute('backend_log_overview');
    }

    /**
     * @Route("/log/overview", name="backend_log_overview")
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request $request
     * @param LogServiceInterface $logService
     * @return Response
     */
    public function overviewAction(Request $request, LogServiceInterface $logService): Response
    {
        return $this->render('@Backend/log/overview.html.twig', [
            'levels' => $logService->getUsedLevels(),
            'search' => $request->get('search')
        ]);
    }

    /**
     * @Route("/log/list/", name="backend_log_getlist", methods={"POST"})
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request $request
     * @param LogServiceInterface $logService
     * @param DatatablesRequestParserInterface $datatablesRequestParser
     * @return Response
     */
    public function getListAction(Request $request, LogServiceInterface $logService, DatatablesRequestParserInterface $datatablesRequestParser): Response
    {
        $datatables = $datatablesRequestParser->parseRequest($request);
        $sortBy = $datatables->getOrder()[0]->getColumn();
        $sortOrder = $datatables->getOrder()[0]->getDir();
        $levelColumn = $datatables->getColumn('level');

        $items = $logService->getAll($datatables->getStart(), $datatables->getLength(), $sortBy, $sortOrder, $levelColumn->getSearch()->getValue(), $datatables->getSearch()->getValue());
        $count = $logService->countAll();
        $filteredCount = $logService->countFiltered($levelColumn->getSearch()->getValue(), $datatables->getSearch()->getValue());

        return $this->json([
            'draw' => time(),
            'recordsTotal' => $count,
            'recordsFiltered' => $filteredCount,
            'data' => $items
        ]);
    }

    /**
     * @Route("/log/details/{id}", name="backend_log_details")
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request $request
     * @param int $id
     * @param LogServiceInterface $logService
     * @return Response
     */
    public function detailsAction(Request $request, int $id, LogServiceInterface $logService): Response
    {
        return $this->render('@Backend/log/details.html.twig', [
            'entry' => $logService->get($id)
        ]);
    }

    /**
     * @Route("/log/clear", name="backend_log_clear")
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request $request
     * @param LogServiceInterface $logService
     * @return Response
     */
    public function clearAction(Request $request, LogServiceInterface $logService): Response
    {
        if ($request->isMethod('POST')) {
            $logService->clear();
        }

        return $this->render('@Backend/log/clear.html.twig');
    }
}
