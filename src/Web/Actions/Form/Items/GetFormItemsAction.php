<?php

namespace App\Web\Actions\Form\Items;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Form;
use App\Database\FormItem;
use App\OpenApiGeneration\Attributes\OpenApiArrayResponse;
use App\OpenApiGeneration\Attributes\OpenApiParameter;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\NoResultException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/form/{id}/item', JinyaAction::GET)]
#[Authenticated(role: Authenticated::READER)]
#[OpenApiRequest('This action gets all form items for the given form')]
#[OpenApiParameter('id', required: true, type: OpenApiParameter::TYPE_INTEGER)]
#[OpenApiArrayResponse('Successfully got the form items', example: [
    'id' => 1,
    'label' => OpenApiResponse::FAKER_WORD,
    'placeholder' => OpenApiResponse::FAKER_WORD,
    'position' => 0,
    'helpText' => OpenApiResponse::FAKER_PARAGRAPH,
    'type' => 'text',
    'options' => ['Option 1'],
    'spamFilter' => ['Random text'],
    'isFromAddress' => false,
    'isRequired' => false,
    'isSubject' => false,
], exampleName: 'List of form items', ref: FormItem::class)]
#[OpenApiResponse('Form not found', example: OpenApiResponse::NOT_FOUND, exampleName: 'Form not found', statusCode: Action::HTTP_NOT_FOUND, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
class GetFormItemsAction extends Action
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
        $id = $this->args['id'];
        $form = Form::findById($id);
        if (!$form) {
            throw new NoResultException($this->request, 'Form not found');
        }

        $items = $form->getItems();

        return $this->respond($this->formatIterator($items));
    }
}
