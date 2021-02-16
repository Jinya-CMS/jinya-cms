<?php

namespace App\Web\Actions\Statistics;

use App\OpenApiGeneration\Attributes\OpenApiParameter;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Statistics\History;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use Psr\Http\Message\ResponseInterface as Response;
use Nyholm\Psr7\Response as NyholmResponse;

#[JinyaAction('/api/statistics/history/{type}', JinyaAction::GET)]
#[Authenticated(Authenticated::WRITER)]
#[OpenApiParameter('type', true, enumValues: ['file', 'form', 'gallery', 'page', 'segment_page'])]
#[OpenApiRequest('This action gets the history statistics for the given entity type')]
#[OpenApiResponse('A successful response', example: [
    'created' => [
        [
            'count' => 16,
            'date' => '2019-12-23'
        ],
        [
            'count' => 1,
            'date' => '2020-03-04'
        ],
        [
            'count' => 6,
            'date' => '2020-08-05'
        ],
        [
            'count' => 6,
            'date' => '2021-01-05'
        ]
    ],
    'updated' => [
        [
            'count' => 7,
            'date' => '2020-01-10'
        ],
        [
            'count' => 7,
            'date' => '2020-03-10'
        ],
        [
            'count' => 7,
            'date' => '2020-08-05'
        ],
        [
            'count' => 1,
            'date' => '2021-01-01'
        ],
        [
            'count' => 6,
            'date' => '2021-01-05'
        ],
        [
            'count' => 1,
            'date' => '2021-01-06'
        ]
    ]
], exampleName: 'Returned history statistics', schema: [
    'created' => [
        'type' => 'array',
        'items' => [
            'type' => 'object',
            'properties' => [
                'count' => ['type' => 'integer'],
                'date' => ['type' => 'string'],
            ],
        ],
    ],
    'updated' => [
        'type' => 'array',
        'items' => [
            'type' => 'object',
            'properties' => [
                'count' => ['type' => 'integer'],
                'date' => ['type' => 'string'],
            ],
        ],
    ],
])]
class GetHistoryStatisticsAction extends Action
{

    /**
     * @inheritDoc
     */
    protected function action(): Response
    {
        $historyStats = new History();
        $type = str_replace('-', '_', $this->args['type']);
        if (!in_array($type, ['file', 'form', 'gallery', 'page', 'segment_page'])) {
            /** @noinspection PhpIncompatibleReturnTypeInspection */
            return (new NyholmResponse())
                ->withStatus(Action::HTTP_MOVED_PERMANENTLY)
                ->withHeader('Location', '/');
        }

        return $this->respond(
            [
                'created' => $this->formatIterator($historyStats->getCreatedHistory($type)),
                'updated' => $this->formatIterator($historyStats->getUpdatedHistory($type)),
            ],
        );
    }
}