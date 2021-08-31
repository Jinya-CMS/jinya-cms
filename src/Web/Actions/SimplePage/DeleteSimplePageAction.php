<?php

namespace App\Web\Actions\SimplePage;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\SimplePage;
use App\OpenApiGeneration\Attributes\OpenApiParameter;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/page/{id}', JinyaAction::DELETE)]
#[Authenticated(Authenticated::WRITER)]
#[OpenApiRequest('This action deletes the given simple page')]
#[OpenApiParameter('id', required: true, type: OpenApiParameter::TYPE_INTEGER)]
#[OpenApiResponse('Successfully deleted the simple page', statusCode: Action::HTTP_NO_CONTENT)]
#[OpenApiResponse('Simple page not found', example: OpenApiResponse::NOT_FOUND, exampleName: 'Simple page not found', statusCode: Action::HTTP_NOT_FOUND, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
class DeleteSimplePageAction extends Action
{
    /**
     * {@inheritDoc}
     * @throws JsonException
     * @throws NoResultException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    public function action(): Response
    {
        $page = SimplePage::findById($this->args['id']);
        if (null === $page) {
            throw new NoResultException($this->request, 'Page not found');
        }

        $page->delete();

        return $this->noContent();
    }
}
