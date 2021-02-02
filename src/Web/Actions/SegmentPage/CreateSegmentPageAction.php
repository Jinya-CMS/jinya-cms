<?php

namespace App\Web\Actions\SegmentPage;

use App\Database\Exceptions\UniqueFailedException;
use App\Database\SegmentPage;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiRequestBody;
use App\OpenApiGeneration\Attributes\OpenApiRequestExample;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Attributes\RequiredFields;
use App\Web\Exceptions\ConflictException;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/segment-page', JinyaAction::POST)]
#[Authenticated(Authenticated::WRITER)]
#[RequiredFields(['name'])]
#[OpenApiRequest('This action create a new segment page')]
#[OpenApiRequestBody([
    'name' => ['type' => 'string'],
])]
#[OpenApiRequestExample('Segment page with required fields', [
    'name' => OpenApiResponse::FAKER_WORD,
])]
#[OpenApiResponse('Successfully created the segment page', example: [
    'name' => OpenApiResponse::FAKER_WORD,
    'id' => 1,
    'segmentCount' => 0,
    'created' => OpenApiResponse::MODIFICATION_EXAMPLE,
    'updated' => OpenApiResponse::MODIFICATION_EXAMPLE,
], exampleName: 'Returned segment page', statusCode: Action::HTTP_CREATED, ref: SegmentPage::class)]
#[OpenApiResponse('Name exists', example: [
    'success' => false,
    'error' => [
        'message' => 'Name exists',
        'type' => 'ConflictException',
    ],
], exampleName: 'Name exists', statusCode: Action::HTTP_CONFLICT, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
class CreateSegmentPageAction extends Action
{
    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function action(): Response
    {
        $body = $this->request->getParsedBody();
        $segmentPage = new SegmentPage();
        $segmentPage->name = $body['name'];

        try {
            $segmentPage->create();
        } catch (UniqueFailedException) {
            throw new ConflictException($this->request, 'Name already used');
        }

        return $this->respond($segmentPage->format(), Action::HTTP_CREATED);
    }
}