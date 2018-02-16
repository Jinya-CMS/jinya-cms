<?php

namespace Jinya\Controller\Backend;

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
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request): Response
    {
        return $this->redirectToRoute('backend_log_overview');
    }

    /**
     * @Route("/log/overview", name="backend_log_overview")
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request $request
     * @return Response
     */
    public function overviewAction(Request $request): Response
    {
        $logService = $this->get('jinya_gallery.services.log_service');

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
     * @return Response
     */
    public function detailsAction(Request $request, int $id): Response
    {
        $logService = $this->get('jinya_gallery.services.log_service');

        return $this->render('@Backend/log/details.html.twig', [
            'entry' => $logService->get($id)
        ]);
    }

    /**
     * @Route("/log/clear", name="backend_log_clear")
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request $request
     * @return Response
     */
    public function clearAction(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $logService = $this->get('jinya_gallery.services.log_service');
            $logService->clear();
        }

        return $this->render('@Backend/log/clear.html.twig');
    }
}
