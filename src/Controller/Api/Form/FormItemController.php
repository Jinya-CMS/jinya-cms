<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 02.03.2018
 * Time: 07:53
 */

namespace Jinya\Controller\Api\Form;


use Jinya\Formatter\Form\FormItemFormatterInterface;
use Jinya\Framework\BaseApiController;
use Jinya\Services\Form\FormItemServiceInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function array_map;

class FormItemController extends BaseApiController
{
    /**
     * @Route("/api/form/{slug}/items", methods={"GET"}, name="api_form_item_get_all")
     *
     * @param string $slug
     * @param FormItemServiceInterface $formItemService
     * @param FormItemFormatterInterface $formItemFormatter
     * @return Response
     */
    public function getAllAction(string $slug, FormItemServiceInterface $formItemService, FormItemFormatterInterface $formItemFormatter): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($slug, $formItemService, $formItemFormatter) {
            return array_map(function ($item) use ($formItemFormatter) {
                return $formItemFormatter
                    ->init($item)
                    ->form()
                    ->helpText()
                    ->options()
                    ->label()
                    ->format();
            }, $formItemService->getItems($slug));
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/form/{slug}/items/{position}", methods={"GET"}, name="api_form_item_get_all")
     *
     * @param string $slug
     * @param int $position
     * @param FormItemServiceInterface $formItemService
     * @param FormItemFormatterInterface $formItemFormatter
     * @return Response
     */
    public function getAction(string $slug, int $position, FormItemServiceInterface $formItemService, FormItemFormatterInterface $formItemFormatter): Response
    {
        return null;
    }
}