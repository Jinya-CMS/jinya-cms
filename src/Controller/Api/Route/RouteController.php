<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 04.03.2018
 * Time: 21:59
 */

namespace Jinya\Controller\Api\Route;

use Jinya\Framework\BaseApiController;
use Jinya\Services\Routing\RouteRetrievalServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface;

class RouteController extends BaseApiController
{
    /**
     * @Route("/api/route/{type}", methods={"GET"}, name="api_route_get_all")
     * @IsGranted("ROLE_WRITER")
     *
     * @param string $type
     * @param RouteRetrievalServiceInterface $routeRetrievalService
     * @return Response
     */
    public function getAllRoutesByType(string $type, RouteRetrievalServiceInterface $routeRetrievalService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($type, $routeRetrievalService) {
            return $routeRetrievalService->retrieveRoutesByType($type);
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/route", methods={"GET"}, name="api_route_types")
     * @IsGranted("ROLE_WRITER")
     *
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function getAllTypes(TranslatorInterface $translator): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($translator) {
            return [
                RouteRetrievalServiceInterface::FORM_DETAIL_ROUTE => $translator->trans('api.route.types.'.RouteRetrievalServiceInterface::FORM_DETAIL_ROUTE),
                RouteRetrievalServiceInterface::GALLERY_DETAIL_ROUTE => $translator->trans('api.route.types.'.RouteRetrievalServiceInterface::GALLERY_DETAIL_ROUTE),
                RouteRetrievalServiceInterface::ARTWORK_DETAIL_ROUTE => $translator->trans('api.route.types.'.RouteRetrievalServiceInterface::ARTWORK_DETAIL_ROUTE),
                RouteRetrievalServiceInterface::PAGE_DETAIL_ROUTE => $translator->trans('api.route.types.'.RouteRetrievalServiceInterface::PAGE_DETAIL_ROUTE),
            ];
        });

        return $this->json($data, $status);
    }
}
