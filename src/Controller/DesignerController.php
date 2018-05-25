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
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     *
     * @return Response
     */
    public function indexAction()
    {
        return $this->render('@Designer/Home/index.html.twig');
    }
}
