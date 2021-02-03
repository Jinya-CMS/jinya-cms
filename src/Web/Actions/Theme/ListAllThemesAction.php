<?php

namespace App\Web\Actions\Theme;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Theme;
use App\OpenApiGeneration\Attributes\OpenApiListResponse;
use App\OpenApiGeneration\Attributes\OpenApiParameter;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/theme', JinyaAction::GET)]
#[Authenticated(Authenticated::WRITER)]
#[OpenApiRequest('This action gets the given theme')]
#[OpenApiParameter('id', required: true, type: OpenApiParameter::TYPE_INTEGER)]
#[OpenApiListResponse('Successfully got the list of themes', example: [
    'configuration' => [
        'page' => [
            'title' => ''
        ],
        'footer' => [
            'copyright' => 'Lorem ipsum'
        ],
        'fonts' => [
        ],
        'input' => [
        ],
        'buttons' => [
            'submit' => ''
        ],
        'dropdowns' => [
        ],
        'messages' => [
        ],
        'profile' => [
        ]
    ],
    'description' => 'The default theme of the jinya cms is modern and provides a good looking user interface.',
    'name' => 'jinya-default-theme',
    'displayName' => 'Jinya Default Theme',
    'scssVariables' => [
        '$jinya-debug' => 'false'
    ],
    'id' => 5
], exampleName: 'List of themes', ref: Theme::class)]
#[OpenApiResponse('Theme not found', example: OpenApiResponse::NOT_FOUND, exampleName: 'Theme not found', statusCode: Action::HTTP_NOT_FOUND, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
class ListAllThemesAction extends ThemeAction
{
    /**
     * {@inheritDoc}
     * @throws JsonException
     * @throws UniqueFailedException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     */
    protected function action(): Response
    {
        $this->syncThemes();
        if (isset($params['keyword'])) {
            return $this->respondList($this->formatIterator(Theme::findByKeyword($params['keyword'])));
        }

        return $this->respondList($this->formatIterator(Theme::findAll()));
    }
}
