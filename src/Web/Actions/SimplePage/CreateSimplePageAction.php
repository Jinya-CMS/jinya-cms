<?php

namespace App\Web\Actions\SimplePage;

use App\Database\Exceptions\UniqueFailedException;
use App\Database\SimplePage;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiRequestBody;
use App\OpenApiGeneration\Attributes\OpenApiRequestExample;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Attributes\RequiredFields;
use App\Web\Exceptions\ConflictException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/page', JinyaAction::POST)]
#[Authenticated(Authenticated::WRITER)]
#[RequiredFields(['title', 'content'])]
#[OpenApiRequest('This action create a new simple page')]
#[OpenApiRequestBody([
    'title' => ['type' => 'string'],
    'content' => ['type' => 'string'],
])]
#[OpenApiRequestExample('Simple page with required fields', [
    'title' => OpenApiResponse::FAKER_WORD,
])]
#[OpenApiRequestExample('Simple page with all fields', [
    'title' => OpenApiResponse::FAKER_WORD,
    'content' => OpenApiResponse::FAKER_PARAGRAPH,
])]
#[OpenApiResponse('Successfully created the simple page', example: [
    'id' => 1,
    'content' => OpenApiResponse::FAKER_PARAGRAPH,
    'title' => OpenApiResponse::FAKER_WORD,
    'created' => OpenApiResponse::MODIFICATION_EXAMPLE,
    'updated' => OpenApiResponse::MODIFICATION_EXAMPLE,
], exampleName: 'Returned file', statusCode: Action::HTTP_CREATED, ref: SimplePage::class)]
#[OpenApiResponse('Title exists', example: [
    'success' => false,
    'error' => [
        'message' => 'Title exists',
        'type' => 'ConflictException',
    ],
], exampleName: 'Title exists', statusCode: Action::HTTP_CONFLICT, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
class CreateSimplePageAction extends Action
{

    /**
     * @inheritDoc
     * @return Response
     * @throws ConflictException
     * @throws UniqueFailedException
     * @throws \App\Database\Exceptions\ForeignKeyFailedException
     * @throws \App\Database\Exceptions\InvalidQueryException
     */
    protected function action(): Response
    {
        $body = $this->request->getParsedBody();

        $page = new SimplePage();
        $page->title = $body['title'];
        $page->content = $body['content'];

        try {
            $page->create();
        } catch (UniqueFailedException) {
            throw new ConflictException($this->request, 'Title is used');
        }

        return $this->respond($page->format(), Action::HTTP_CREATED);
    }
}