<?php

namespace App\Web\Actions\SegmentPage\Segments;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\File;
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

#[JinyaAction('/api/segment-page/{id}/segment/file', JinyaAction::POST)]
#[Authenticated(Authenticated::WRITER)]
#[RequiredFields(['file', 'action', 'position'])]
#[OpenApiRequest('This action create a new file segment')]
#[OpenApiRequestBody([
    'target' => ['type' => 'string'],
    'action' => ['type' => 'string'],
    'position' => ['type' => 'integer'],
    'file' => ['type' => 'integer'],
])]
#[OpenApiParameter('id', required: true, type: OpenApiParameter::TYPE_INTEGER)]
#[OpenApiRequestExample('File segment with required fields', [
    'file' => 0,
    'position' => 0,
    'action' => 'none',
])]
#[OpenApiRequestExample('File segment with all fields', [
    'target' => OpenApiResponse::FAKER_URL,
    'action' => 'link',
    'position' => 0,
    'file' => 0,
])]
#[OpenApiResponse('Successfully created file segment', example: [
    'file' => [
        'id' => 0,
        'name' => OpenApiResponse::FAKER_WORD,
        'type' => OpenApiResponse::FAKER_MIMETYPE,
        'path' => OpenApiResponse::FAKER_SHA1,
    ],
    'id' => 0,
    'position' => 0,
    'action' => 'link',
    'target' => OpenApiResponse::FAKER_URL,
], exampleName: 'Successfully created file segment', statusCode: Action::HTTP_CREATED, ref: Segment::class)]
#[OpenApiResponse('Segment page not found', example: OpenApiResponse::NOT_FOUND, exampleName: 'Segment page not found', statusCode: Action::HTTP_NOT_FOUND, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
class CreateFileSegmentAction extends Action
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

        $fileId = $body['file'];
        if (!File::findById($fileId)) {
            throw new NoResultException($this->request, 'File not found');
        }

        $segment = new Segment();
        $segment->pageId = $id;
        $segment->fileId = $body['file'];
        $segment->galleryId = null;
        $segment->formId = null;
        $segment->html = null;
        $segment->script = $body['script'] ?? '';
        $segment->target = $body['target'] ?? '';
        $segment->action = $body['action'] ?? '';
        $segment->position = $body['position'];

        $segment->create();

        return $this->respond($segment->format(), Action::HTTP_CREATED);
    }
}
