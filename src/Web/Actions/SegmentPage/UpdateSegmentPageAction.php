<?php

namespace App\Web\Actions\SegmentPage;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\SegmentPage;
use App\OpenApiGeneration\Attributes\OpenApiParameter;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiRequestBody;
use App\OpenApiGeneration\Attributes\OpenApiRequestExample;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\ConflictException;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/segment-page/{id}', JinyaAction::PUT)]
#[Authenticated(Authenticated::WRITER)]
#[OpenApiParameter('id', required: true, type: OpenApiParameter::TYPE_INTEGER)]
#[OpenApiRequest('This action updated the given segment page')]
#[OpenApiRequestBody([
    'name' => ['type' => 'string'],
])]
#[OpenApiRequestExample('Segment page with required fields', [
    'name' => OpenApiResponse::FAKER_WORD,
])]
#[OpenApiResponse('Successfully updated the segment page', statusCode: Action::HTTP_NO_CONTENT)]
#[OpenApiResponse('Name exists', example: [
    'success' => false,
    'error' => [
        'message' => 'Name exists',
        'type' => 'ConflictException',
    ],
], exampleName: 'Name exists', statusCode: Action::HTTP_CONFLICT, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
class UpdateSegmentPageAction extends Action
{

    /**
     * @inheritDoc
     * @return Response
     * @throws ConflictException
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
        if ($segmentPage === null) {
            throw new NoResultException($this->request, 'Segment page not found');
        }

        if (isset($body['name'])) {
            $segmentPage->name = $body['name'];
        }

        try {
            $segmentPage->update();
        } catch (UniqueFailedException) {
            throw new ConflictException($this->request, 'Name already used');
        }

        return $this->noContent();
    }
}