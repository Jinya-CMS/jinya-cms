<?php

namespace App\Web\Actions\Statistics;

use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Statistics\Entity;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/statistics/entity', JinyaAction::GET)]
#[Authenticated(Authenticated::WRITER)]
#[OpenApiRequest('This action gets the entity statistics')]
#[OpenApiResponse('A successful response', example: [
    'files' => 255,
    'galleries' => 69,
    'simplePages' => 6,
    'segmentPages' => 15,
    'forms' => 5,
], exampleName: 'Returned entity statistics', schema: [
    'files' => ['type' => 'integer'],
    'galleries' => ['type' => 'integer'],
    'simplePages' => ['type' => 'integer'],
    'segmentPages' => ['type' => 'integer'],
    'forms' => ['type' => 'integer'],
])]
class EntityStatisticsAction extends Action
{

    /**
     * @inheritDoc
     */
    protected function action(): Response
    {
        $entityStats = new Entity();
        return $this->respond($entityStats->getEntityShare());
    }
}