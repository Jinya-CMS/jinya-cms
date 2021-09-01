<?php

namespace App\Web\Actions\SegmentPage\Segments;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Form;
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

#[JinyaAction('/api/segment-page/{id}/segment/form', JinyaAction::POST)]
#[Authenticated(Authenticated::WRITER)]
#[RequiredFields(['form', 'position'])]
#[OpenApiRequest('This action create a new form segment')]
#[OpenApiRequestBody([
    'position' => ['type' => 'integer'],
    'form' => ['type' => 'integer'],
])]
#[OpenApiParameter('id', required: true, type: OpenApiParameter::TYPE_INTEGER)]
#[OpenApiRequestExample('Form segment with required fields', [
    'form' => 0,
    'position' => 0,
])]
#[OpenApiRequestExample('Form segment with all fields', [
    'position' => 0,
    'form' => 0,
])]
#[OpenApiResponse('Successfully created form segment', example: [
    'form' => [
        'id' => 0,
        'title' => OpenApiResponse::FAKER_WORD,
        'description' => OpenApiResponse::FAKER_PARAGRAPH,
        'toAddress' => OpenApiResponse::FAKER_EMAIL,
    ],
    'id' => 0,
    'position' => 0,
], exampleName: 'Successfully created form segment', statusCode: Action::HTTP_CREATED, ref: Segment::class)]
#[OpenApiResponse('Segment page not found', example: OpenApiResponse::NOT_FOUND, exampleName: 'Segment page not found', statusCode: Action::HTTP_NOT_FOUND, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
class CreateFormSegmentAction extends Action
{
    /**
     * {@inheritDoc}
     * @return Response
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     * @throws UniqueFailedException
     */
    protected function action(): Response
    {
        $body = $this->request->getParsedBody();
        $id = $this->args['id'];
        $segmentPage = SegmentPage::findById($id);

        if (!$segmentPage) {
            throw new NoResultException($this->request, 'Segment page not found');
        }

        $formId = $body['form'];
        if (!Form::findById($formId)) {
            throw new NoResultException($this->request, 'Form not found');
        }

        $segment = new Segment();
        $segment->pageId = $id;
        $segment->html = null;
        $segment->fileId = null;
        $segment->galleryId = null;
        $segment->formId = $formId;
        $segment->script = null;
        $segment->target = null;
        $segment->action = null;
        $segment->position = $body['position'];

        $segment->create();

        return $this->respond($segment->format(), Action::HTTP_CREATED);
    }
}
