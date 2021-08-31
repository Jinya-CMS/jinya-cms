<?php

namespace App\Web\Actions\Theme;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\SegmentPage;
use App\Database\Theme;
use App\Database\ThemeSegmentPage;
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

#[JinyaAction('/api/theme/{id}/segment-page/{name}', JinyaAction::PUT)]
#[Authenticated(Authenticated::WRITER)]
#[OpenApiRequest('This action updates the given theme segment page')]
#[OpenApiParameter('id', required: true, type: OpenApiParameter::TYPE_INTEGER)]
#[OpenApiParameter('name', required: true, type: OpenApiParameter::TYPE_STRING)]
#[OpenApiRequestBody(['segmentPage' => ['type' => 'integer']])]
#[OpenApiResponse('Successfully updated the theme segment page', statusCode: Action::HTTP_NO_CONTENT)]
#[OpenApiResponse('Theme or segment page not found', example: OpenApiResponse::NOT_FOUND, exampleName: 'Theme or segment page not found', statusCode: Action::HTTP_NOT_FOUND, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
class PutThemeSegmentPageAction extends ThemeAction
{
    /**
     * @throws JsonException
     * @throws NoResultException
     * @throws UniqueFailedException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     */
    public function action(): Response
    {
        $this->syncThemes();
        $themeId = $this->args['id'];
        $name = $this->args['name'];
        $theme = Theme::findById($themeId);

        if (!$theme) {
            throw new NoResultException($this->request, 'Theme not found');
        }

        $body = $this->request->getParsedBody();
        $segmentPageId = $body['segmentPage'];
        $segmentPage = SegmentPage::findById($segmentPageId);
        if (!$segmentPage) {
            throw new NoResultException($this->request, 'SegmentPage not found');
        }

        $themeSegmentPage = ThemeSegmentPage::findByThemeAndName($themeId, $name);
        if (null !== $themeSegmentPage) {
            $themeSegmentPage->themeId = $themeId;
            $themeSegmentPage->segmentPageId = $segmentPage->id;
            $themeSegmentPage->name = $name;
            $themeSegmentPage->update();
        } else {
            $themeSegmentPage = new ThemeSegmentPage();
            $themeSegmentPage->themeId = $themeId;
            $themeSegmentPage->segmentPageId = $segmentPage->id;
            $themeSegmentPage->name = $name;
            $themeSegmentPage->create();
        }

        return $this->noContent();
    }
}
