<?php

namespace App\Web\Actions\Theme;

use App\Database;
use App\OpenApiGeneration\Attributes\OpenApiParameter;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Theming;
use App\Web\Actions\Action;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Nyholm\Psr7\Stream;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/theme/{id}/preview', JinyaAction::GET)]
#[OpenApiRequest('This action gets the preview image of the given theme')]
#[OpenApiParameter('id', required: true, type: OpenApiParameter::TYPE_INTEGER)]
#[OpenApiResponse('Successfully got the preview image')]
#[OpenApiResponse('Theme not found', example: OpenApiResponse::NOT_FOUND, exampleName: 'Theme not found', statusCode: Action::HTTP_NOT_FOUND, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
class GetPreviewImageAction extends ThemeAction
{
    /**
     * {@inheritDoc}
     * @throws Database\Exceptions\ForeignKeyFailedException
     * @throws Database\Exceptions\InvalidQueryException
     * @throws Database\Exceptions\UniqueFailedException
     * @throws NoResultException
     * @throws JsonException
     */
    public function action(): Response
    {
        $themeId = $this->args['id'];
        $dbTheme = Database\Theme::findById($themeId);
        if (!$dbTheme) {
            throw new NoResultException($this->request, 'Theme not found');
        }

        $theme = new Theming\Theme($dbTheme);
        if (file_exists($theme->getPreviewImagePath())) {
            return $this->response
                ->withBody(Stream::create(fopen($theme->getPreviewImagePath(), 'rb')))
                ->withHeader('Content-Type', 'application/octet-stream')
                ->withStatus(self::HTTP_OK);
        }

        return $this->noContent();
    }
}
