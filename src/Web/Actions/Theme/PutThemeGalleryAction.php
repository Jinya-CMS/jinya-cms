<?php

namespace App\Web\Actions\Theme;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Gallery;
use App\Database\Theme;
use App\Database\ThemeGallery;
use App\OpenApiGeneration\Attributes\OpenApiParameter;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiRequestBody;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/theme/{id}/gallery/{name}', JinyaAction::PUT)]
#[Authenticated(Authenticated::WRITER)]
#[OpenApiRequest('This action updates the given theme gallery')]
#[OpenApiParameter('id', required: true, type: OpenApiParameter::TYPE_INTEGER)]
#[OpenApiParameter('name', required: true, type: OpenApiParameter::TYPE_STRING)]
#[OpenApiRequestBody(['gallery' => ['type' => 'integer']])]
#[OpenApiResponse('Successfully updated the theme gallery', statusCode: Action::HTTP_NO_CONTENT)]
#[OpenApiResponse('Theme or gallery not found', example: OpenApiResponse::NOT_FOUND, exampleName: 'Theme or gallery not found', statusCode: Action::HTTP_NOT_FOUND, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
class PutThemeGalleryAction extends ThemeAction
{
    /**
     * @throws JsonException
     * @throws NoResultException
     * @throws UniqueFailedException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     */
    protected function action(): Response
    {
        $this->syncThemes();
        $themeId = $this->args['id'];
        $name = $this->args['name'];
        $theme = Theme::findById($themeId);

        if (!$theme) {
            throw new NoResultException($this->request, 'Theme not found');
        }

        $body = $this->request->getParsedBody();
        $galleryId = $body['gallery'];
        $gallery = Gallery::findById($galleryId);
        if (!$gallery) {
            throw new NoResultException($this->request, 'Gallery not found');
        }

        $themeGallery = ThemeGallery::findByThemeAndName($themeId, $name);
        if (null !== $themeGallery) {
            $themeGallery->themeId = $themeId;
            $themeGallery->galleryId = $gallery->id;
            $themeGallery->name = $name;
            $themeGallery->update();
        } else {
            $themeGallery = new ThemeGallery();
            $themeGallery->themeId = $themeId;
            $themeGallery->galleryId = $gallery->id;
            $themeGallery->name = $name;
            $themeGallery->create();
        }

        return $this->noContent();
    }
}
