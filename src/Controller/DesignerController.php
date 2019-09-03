<?php

namespace Jinya\Controller;

use Jinya\Framework\BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DesignerController extends BaseController
{
    /**
     * @Route("/designer", name="designer_home_index")
     * @Route("/designer/{route}", name="designer_home_index_specific", requirements={"route": ".*"})
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        return $this->render('@Jinya/Designer/Home/index.html.twig');
    }
}
