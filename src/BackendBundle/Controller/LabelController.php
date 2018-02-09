<?php

namespace BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LabelController extends Controller
{
    /**
     * @Route("/label/add", name="backend_label_add", methods={"POST"})
     *
     * @param Request $request
     * @return Response
     */
    public function addAction(Request $request): Response
    {
        $labelService = $this->get('jinya_gallery.services.label_service');

        $name = $request->get('name');
        $labelService->addLabel($name);

        return $this->redirect($request->headers->get('referer'));
    }
}
