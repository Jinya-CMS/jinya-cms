<?php

namespace Jinya\Controller\Backend;

use Jinya\Services\Labels\LabelServiceInterface;
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
     * @param LabelServiceInterface $labelService
     * @return Response
     */
    public function addAction(Request $request, LabelServiceInterface $labelService): Response
    {
        $name = $request->get('name');
        $labelService->addLabel($name);

        return $this->redirect($request->headers->get('referer'));
    }
}
