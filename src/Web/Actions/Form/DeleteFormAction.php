<?php

namespace App\Web\Actions\Form;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Form;
use App\OpenApiGeneration\Attributes\OpenApiParameter;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/form/{id}', JinyaAction::DELETE)]
#[Authenticated(role: Authenticated::WRITER)]
#[OpenApiRequest('This action deletes the given form')]
#[OpenApiParameter('id', required: true, type: OpenApiParameter::TYPE_INTEGER)]
#[OpenApiResponse('Successfully deleted the form', statusCode: Action::HTTP_NO_CONTENT)]
#[OpenApiResponse('Form not found', example: OpenApiResponse::NOT_FOUND, exampleName: 'Form not found', statusCode: Action::HTTP_NOT_FOUND, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
class DeleteFormAction extends Action
{
    /**
     * {@inheritDoc}
     * @throws JsonException
     * @throws NoResultException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    protected function action(): Response
    {
        $form = Form::findById($this->args['id']);
        if (null === $form) {
            throw new NoResultException($this->request, 'Form not found');
        }

        $form->delete();

        return $this->noContent();
    }
}
