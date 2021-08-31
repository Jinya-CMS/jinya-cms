<?php

namespace App\Web\Actions\Form\Items;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\FormItem;
use App\OpenApiGeneration\Attributes\OpenApiParameter;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/form/{id}/item/{position}', JinyaAction::DELETE)]
#[Authenticated(role: Authenticated::WRITER)]
#[OpenApiRequest('This action deletes the given form item')]
#[OpenApiParameter('id', required: true, type: OpenApiParameter::TYPE_INTEGER)]
#[OpenApiParameter('position', required: true, type: OpenApiParameter::TYPE_INTEGER)]
#[OpenApiResponse('Successfully deleted the form item', statusCode: Action::HTTP_NO_CONTENT)]
#[OpenApiResponse('Form or form item not found', example: OpenApiResponse::NOT_FOUND, exampleName: 'Form or form item not found', statusCode: Action::HTTP_NOT_FOUND, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
class DeleteFormItemAction extends Action
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
        $formItem = FormItem::findByPosition($this->args['id'], $this->args['position']);
        if (!$formItem) {
            throw new NoResultException($this->request, 'Form item not found');
        }

        $formItem->delete();

        return $this->noContent();
    }
}
