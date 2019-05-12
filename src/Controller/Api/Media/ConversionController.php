<?php

namespace Jinya\Controller\Api\Media;

use Jinya\Framework\BaseApiController;
use Jinya\Services\Media\ConversionServiceInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConversionController extends BaseApiController
{
    /**
     * @Route("/api/media/conversion/type")
     *
     * @param ConversionServiceInterface $conversionService
     * @return Response
     */
    public function getAllTypes(ConversionServiceInterface $conversionService): Response
    {
        return $this->json($conversionService->getSupportedTypes());
    }
}