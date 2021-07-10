<?php

namespace App\Web\Actions\Statistics;

use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Statistics\Storage;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/statistics/system', JinyaAction::GET)]
#[Authenticated(Authenticated::WRITER)]
#[OpenApiRequest('This action gets the system statistics')]
#[OpenApiResponse('A successful response', example: [
    'storage' => [
        'used' => 65441653416,
        'free' => 654685465415163,
        'total' => 56465464565646541,
    ],
], exampleName: 'Returned system statistics', schema: [
    'storage' => [
        'type' => 'object',
        'properties' => [
            'used' => ['type' => 'integer'],
            'free' => ['type' => 'integer'],
            'total' => ['type' => 'integer'],
        ],
    ],
])]
class GetSystemStatisticsAction extends Action
{

    /**
     * @inheritDoc
     */
    protected function action(): Response
    {
        $storageStats = new Storage();
        return $this->respond(
            [
                'used' => $storageStats->getUsedStorage(),
                'free' => $storageStats->getFreeStorage(),
                'total' => $storageStats->getTotalStorage(),
            ]
        );
    }
}