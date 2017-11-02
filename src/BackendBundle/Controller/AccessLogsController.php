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
     * @Route("/accesslog/list", name="backend_accesslog_getlist")
     * @param Request $request
     */
    public function getListAction(Request $request)
    {
        return $this->json([]);
    }
}
