<?php

namespace Jinya\Controller\Designer;

use Jinya\Services\Routing\RouteRetrievalServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RouteController extends Controller
{
    /**
     * @Route("/designer/route/api/", name="designer_route_retrieve")
     *
     * @param Request $request
     * @param RouteRetrievalServiceInterface $routeRetrievalService
     * @return Response
     */
    public function retrieveAction(Request $request, RouteRetrievalServiceInterface $routeRetrievalService): Response
    {
        $type = $request->get('type', -1);
        $items = $routeRetrievalService->retrieveRoutesByType($type);

        return $this->json([
            'routes' => $items,
            'success' => true
        ]);
    }
}
