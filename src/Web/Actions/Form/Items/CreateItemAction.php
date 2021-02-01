<?php

namespace App\Web\Actions\Form\Items;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Form;
use App\Database\FormItem;
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
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/form/{id}/item', JinyaAction::POST)]
#[Authenticated(role: Authenticated::WRITER)]
#[RequiredFields(['label', 'position'])]
#[OpenApiRequest('This action create a new form item')]
#[OpenApiRequestBody([
    'label' => ['type' => 'string'],
    'placeholder' => ['type' => 'string'],
    'position' => ['type' => 'string'],
    'helpText' => ['type' => 'string'],
    'type' => ['type' => 'string'],
    'options' => ['type' => 'array', 'items' => ['type' => 'string']],
    'spamFilter' => ['type' => 'array', 'items' => ['type' => 'string']],
    'isFromAddress' => ['type' => 'boolean'],
    'isRequired' => ['type' => 'boolean'],
    'isSubject' => ['type' => 'boolean'],
])]
#[OpenApiParameter('id', required: true, type: OpenApiParameter::TYPE_INTEGER)]
#[OpenApiRequestExample('Form item with all fields', [
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
])]
#[OpenApiRequestExample('Form item with required fields', [
    'label' => OpenApiResponse::FAKER_WORD,
    'position' => 0,
])]
#[OpenApiResponse('Successfully created the form item', example: [
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
], exampleName: 'Created form item', statusCode: Action::HTTP_CREATED, ref: FormItem::class)]
#[OpenApiResponse('Form not found', example: OpenApiResponse::NOT_FOUND, exampleName: 'Form not found', statusCode: Action::HTTP_NOT_FOUND, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
class CreateItemAction extends Action
{
    /**
     * {@inheritDoc}
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
        $form = Form::findById($id);

        if (!$form) {
            throw new NoResultException($this->request, 'Form not found');
        }

        $formItem = new FormItem();
        $formItem->formId = $form->id;
        $formItem->label = $body['label'];
        $formItem->placeholder = $body['placeholder'] ?? '';
        $formItem->position = $body['position'];
        $formItem->helpText = $body['helpText'] ?? '';
        $formItem->type = $body['type'] ?? 'text';
        $formItem->options = $body['options'] ?? [];
        $formItem->spamFilter = $body['spamFilter'] ?? [];
        $formItem->isFromAddress = $body['isFromAddress'] ?? false;
        $formItem->isRequired = $body['isRequired'] ?? false;
        $formItem->isSubject = $body['isSubject'] ?? false;

        $formItem->create();

        return $this->respond($formItem->format(), Action::HTTP_CREATED);
    }
}
