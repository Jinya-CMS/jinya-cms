<?php

namespace App\Web\Actions\SegmentPage\Segments;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
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
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/segment-page/{id}/segment/html', JinyaAction::POST)]
#[Authenticated(Authenticated::WRITER)]
#[RequiredFields(['html', 'position'])]
#[OpenApiRequest('This action create a new html segment')]
#[OpenApiRequestBody([
    'position' => ['type' => 'integer'],
    'html' => ['type' => 'integer'],
])]
#[OpenApiParameter('id', required: true, type: OpenApiParameter::TYPE_INTEGER)]
#[OpenApiRequestExample('Html segment with all fields', [
    'position' => 0,
    'html' => OpenApiResponse::FAKER_PARAGRAPH,
])]
#[OpenApiResponse('Successfully created html segment', example: [
    'html' => [
        'id' => 0,
        'title' => OpenApiResponse::FAKER_WORD,
        'description' => OpenApiResponse::FAKER_PARAGRAPH,
        'toAddress' => OpenApiResponse::FAKER_EMAIL,
    ],
    'id' => 0,
    'position' => 0,
], exampleName: 'Successfully created html segment', statusCode: Action::HTTP_CREATED, ref: Segment::class)]
#[OpenApiResponse('Segment page not found', example: OpenApiResponse::NOT_FOUND, exampleName: 'Segment page not found', statusCode: Action::HTTP_NOT_FOUND, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
class CreateHtmlSegmentAction extends Action
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
        $body = $this->request->getParsedBody();
        $id = $this->args['id'];
        $segmentPage = SegmentPage::findById($id);

        if (!$segmentPage) {
            throw new NoResultException($this->request, 'Segment page not found');
        }

        $segment = new Segment();
        $segment->pageId = $id;
        $segment->html = $body['html'];
        $segment->fileId = null;
        $segment->galleryId = null;
        $segment->formId = null;
        $segment->script = null;
        $segment->target = null;
        $segment->action = null;
        $segment->position = $body['position'];

        $segment->create();

        return $this->respond($segment->format(), Action::HTTP_CREATED);
    }
}
