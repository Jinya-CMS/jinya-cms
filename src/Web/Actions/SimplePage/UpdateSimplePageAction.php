<?php

namespace App\Web\Actions\SimplePage;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\File;
use App\Database\SimplePage;
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

#[JinyaAction('/api/page/{id}', JinyaAction::PUT)]
#[Authenticated(Authenticated::WRITER)]
#[OpenApiRequest('This action updates the given simple page')]
#[OpenApiParameter('id', required: true, type: OpenApiParameter::TYPE_INTEGER)]
#[OpenApiRequestBody([
    'title' => ['type' => 'string'],
    'content' => ['type' => 'string'],
])]
#[OpenApiRequestExample('Simple page with all fields', [
    'title' => OpenApiResponse::FAKER_WORD,
    'content' => OpenApiResponse::FAKER_PARAGRAPH,
])]
#[OpenApiResponse('Successfully updated the simple page', statusCode: Action::HTTP_NO_CONTENT)]
#[OpenApiResponse('Title exists', example: [
    'success' => false,
    'error' => [
        'message' => 'Title exists',
        'type' => 'ConflictException',
    ],
], exampleName: 'Title exists', statusCode: Action::HTTP_CONFLICT, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
#[OpenApiResponse('Simple page not found', example: OpenApiResponse::NOT_FOUND, exampleName: 'Simple page not found', statusCode: Action::HTTP_NOT_FOUND, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
class UpdateSimplePageAction extends Action
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
        $page = SimplePage::findById($id);
        if ($page === null) {
            throw new NoResultException($this->request, 'Page not found');
        }

        if (isset($body['title'])) {
            $page->title = $body['title'];
        }

        if (isset($body['content'])) {
            $page->content = $body['content'];
        }

        try {
            $page->update();
        } catch (UniqueFailedException) {
            throw new ConflictException($this->request, 'Title already used');
        }

        return $this->noContent();
    }
}