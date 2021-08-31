<?php

namespace App\Web\Actions\Theme;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Theme;
use App\OpenApiGeneration\Attributes\OpenApiParameter;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/theme/{id}', JinyaAction::GET)]
#[Authenticated(Authenticated::WRITER)]
#[OpenApiRequest('This action gets the given theme')]
#[OpenApiParameter('id', required: true, type: OpenApiParameter::TYPE_INTEGER)]
#[OpenApiResponse(
    'Successfully got the theme', example: [
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
], exampleName: 'Jinya default theme', ref: Theme::class)]
#[OpenApiResponse('Theme not found', example: OpenApiResponse::NOT_FOUND, exampleName: 'Theme not found', statusCode: Action::HTTP_NOT_FOUND, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
class GetThemeAction extends ThemeAction
{
    /**
     * {@inheritDoc}
     * @return Response
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     * @throws UniqueFailedException
     */
    public function action(): Response
    {
        $themeId = $this->args['id'];
        $theme = Theme::findById($themeId);
        if (!$theme) {
            throw new NoResultException($this->request, 'Theme not found');
        }

        return $this->respond($theme->format());
    }
}
