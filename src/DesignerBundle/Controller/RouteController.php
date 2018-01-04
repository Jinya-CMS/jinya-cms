<?php

namespace DesignerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RouteController extends Controller
{
    /**
     * @Route("/route/api/", name="designer_route_retrieve")
     *
     * @param Request $request
     * @return Response
     */
    public function retrieveAction(Request $request): Response
    {
        $type = $request->get('type', -1);

        if ($type === -1) {
            return $this->json([]);
        } else {
            $items = [];

            return $this->json($items);
        }
    }
}
