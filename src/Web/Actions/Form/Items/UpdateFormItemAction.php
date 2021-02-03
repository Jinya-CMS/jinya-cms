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
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/form/{id}/item/{position}', JinyaAction::PUT)]
#[Authenticated(role: Authenticated::WRITER)]
#[OpenApiRequest('This action updates the given form item')]
#[OpenApiRequestBody([
    'label' => ['type' => 'string'],
    'placeholder' => ['type' => 'string'],
    'position' => ['type' => 'integer'],
    'helpText' => ['type' => 'string'],
    'type' => ['type' => 'string'],
    'options' => ['type' => 'array', 'items' => ['type' => 'string']],
    'spamFilter' => ['type' => 'array', 'items' => ['type' => 'string']],
    'isFromAddress' => ['type' => 'boolean'],
    'isRequired' => ['type' => 'boolean'],
    'isSubject' => ['type' => 'boolean'],
])]
#[OpenApiParameter('id', required: true, type: OpenApiParameter::TYPE_INTEGER)]
#[OpenApiParameter('position', required: true, type: OpenApiParameter::TYPE_INTEGER)]
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
#[OpenApiResponse('Successfully updated the form item', statusCode: Action::HTTP_NO_CONTENT)]
#[OpenApiResponse('Form or form item not found', example: OpenApiResponse::NOT_FOUND, exampleName: 'Form or form item not found', statusCode: Action::HTTP_NOT_FOUND, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
class UpdateFormItemAction extends Action
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
        $formItem = FormItem::findByPosition($this->args['id'], $this->args['position']);
        if (!$formItem) {
            throw new NoResultException($this->request, 'Form item not found');
        }

        if (isset($body['label'])) {
            $formItem->label = $body['label'];
        }

        if (isset($body['placeholder'])) {
            $formItem->placeholder = $body['placeholder'];
        }

        if (isset($body['helpText'])) {
            $formItem->helpText = $body['helpText'];
        }

        if (isset($body['type'])) {
            $formItem->helpText = $body['type'];
        }

        if (isset($body['options'])) {
            $formItem->options = $body['options'];
        }

        if (isset($body['spamFilter'])) {
            $formItem->spamFilter = $body['spamFilter'];
        }

        if (isset($body['isFromAddress'])) {
            $formItem->isFromAddress = $body['isFromAddress'];
        }

        if (isset($body['isRequired'])) {
            $formItem->isRequired = $body['isRequired'];
        }

        if (isset($body['isSubject'])) {
            $formItem->isSubject = $body['isSubject'];
        }

        $formItem->update();

        if (isset($body['newPosition'])) {
            $formItem->move($body['newPosition']);
        }

        return $this->noContent();
    }
}
