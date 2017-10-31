<?php

namespace BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LogsController extends Controller
{
    /**
     * @Route("/log", name="backend_log_index")
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        return $this->render('');
    }

    /**
     * @Route("/log/access", name="backend_log_access")
     *
     * @return Response
     */
    public function accessLogAction(): Response
    {
        return $this->render('');
    }
}
