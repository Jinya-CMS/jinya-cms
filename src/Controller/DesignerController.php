<?php

namespace Jinya\Controller;

use Jinya\Framework\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DesignerController extends BaseController
{
    /**
     * @Route("/designer", name="designer_home_index")
     *
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        return $this->render('@Designer/default/index.html.twig');
    }
}
