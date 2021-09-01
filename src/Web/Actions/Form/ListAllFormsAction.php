<?php

namespace App\Web\Actions\Form;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Form;
use App\OpenApiGeneration\Attributes\OpenApiListResponse;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/form', JinyaAction::GET)]
#[Authenticated(role: Authenticated::READER)]
#[OpenApiRequest('This action gets the given form')]
#[OpenApiListResponse('Successfully got the form', example: [
    'id' => 1,
    'title' => OpenApiResponse::FAKER_WORD,
    'toAddress' => OpenApiResponse::FAKER_EMAIL,
    'description' => OpenApiResponse::FAKER_PARAGRAPH,
    'created' => OpenApiResponse::MODIFICATION_EXAMPLE,
    'updated' => OpenApiResponse::MODIFICATION_EXAMPLE,
], exampleName: 'The requested form', statusCode: Action::HTTP_CREATED, ref: Form::class)]
#[OpenApiResponse('Form not found', example: OpenApiResponse::NOT_FOUND, exampleName: 'Form not found', statusCode: Action::HTTP_NOT_FOUND, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
class ListAllFormsAction extends Action
{
    /**
     * {@inheritDoc}
     * @throws JsonException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    protected function action(): Response
    {
        $params = $this->request->getQueryParams();
        if (isset($params['keyword'])) {
            return $this->respondList($this->formatIterator(Form::findByKeyword($params['keyword'])));
        }

        return $this->respondList($this->formatIterator(Form::findAll()));
    }
}
