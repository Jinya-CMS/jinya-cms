<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 01.03.2018
 * Time: 07:54
 */

namespace Jinya\Controller\Api\Form;

use Jinya\Framework\BaseApiController;
use Jinya\Services\Form\FormServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FormController extends BaseApiController
{
    /**
     * @Route("/api/form", methods={"GET"}, name="api_form_get_all")
     * @IsGranted("ROLE_WRITER")
     *
     * @param Request $request
     * @param FormServiceInterface $formService
     * @return Response
     */
    public function getAll(Request $request, FormServiceInterface $formService): Response
    {

    }
}