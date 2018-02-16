<?php

namespace Jinya\Controller\Designer;

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
     * @return Response
     */
    public function retrieveAction(Request $request): Response
    {
        $type = $request->get('type', -1);
        $routeRetrievalService = $this->get('jinya_gallery.services_routing.route_retrieval_service');
        $items = $routeRetrievalService->retrieveRoutesByType($type);

        return $this->json([
            'routes' => $items,
            'success' => true
        ]);
    }
}
