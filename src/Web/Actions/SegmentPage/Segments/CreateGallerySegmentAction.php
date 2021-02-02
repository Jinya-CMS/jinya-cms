<?php

namespace App\Web\Actions\SegmentPage\Segments;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Gallery;
use App\Database\Segment;
use App\Database\SegmentPage;
use App\OpenApiGeneration\Attributes\OpenApiParameter;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiRequestBody;
use App\OpenApiGeneration\Attributes\OpenApiRequestExample;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Attributes\RequiredFields;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/segment-page/{id}/segment/gallery', JinyaAction::POST)]
#[Authenticated(Authenticated::WRITER)]
#[RequiredFields(['gallery', 'position'])]
#[OpenApiRequest('This action create a new gallery segment')]
#[OpenApiRequestBody([
    'position' => ['type' => 'integer'],
    'gallery' => ['type' => 'integer'],
])]
#[OpenApiParameter('id', required: true, type: OpenApiParameter::TYPE_INTEGER)]
#[OpenApiRequestExample('Gallery segment with required fields', [
    'gallery' => 0,
    'position' => 0,
])]
#[OpenApiRequestExample('Gallery segment with all fields', [
    'position' => 0,
    'gallery' => 0,
])]
#[OpenApiResponse('Successfully created gallery segment', example: [
    'gallery' => [
        'id' => 0,
        'name' => OpenApiResponse::FAKER_WORD,
        'description' => OpenApiResponse::FAKER_PARAGRAPH,
        'type' => Gallery::TYPE_MASONRY,
        'orientation' => Gallery::ORIENTATION_VERTICAL,
    ],
    'id' => 0,
    'position' => 0,
], exampleName: 'Successfully created gallery segment', statusCode: Action::HTTP_CREATED, ref: Segment::class)]
#[OpenApiResponse('Segment page not found', example: OpenApiResponse::NOT_FOUND, exampleName: 'Segment page not found', statusCode: Action::HTTP_NOT_FOUND, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
class CreateGallerySegmentAction extends Action
{
    /**
     * {@inheritDoc}
     * @throws JsonException
     * @throws NoResultException
     * @throws UniqueFailedException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     */
    protected function action(): Response
    {
        $body = $this->request->getParsedBody();
        $id = $this->args['id'];
        $segmentPage = SegmentPage::findById($id);

        if (!$segmentPage) {
            throw new NoResultException($this->request, 'Segment page not found');
        }

        $galleryId = $body['gallery'];
        if (!Gallery::findById($galleryId)) {
            throw new NoResultException($this->request, 'Gallery not found');
        }

        $segment = new Segment();
        $segment->pageId = $id;
        $segment->html = null;
        $segment->fileId = null;
        $segment->galleryId = $galleryId;
        $segment->formId = null;
        $segment->script = null;
        $segment->target = null;
        $segment->action = null;
        $segment->position = $body['position'];

        $segment->create();

        return $this->respond($segment->format(), Action::HTTP_CREATED);
    }
}
