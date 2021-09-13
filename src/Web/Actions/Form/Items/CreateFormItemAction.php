<?php

namespace App\Web\Actions\Form\Items;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Form;
use App\Database\FormItem;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Attributes\RequiredFields;
use App\Web\Exceptions\NoResultException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/form/{id}/item', JinyaAction::POST)]
#[Authenticated(role: Authenticated::WRITER)]
#[RequiredFields(['label', 'position'])]
class CreateFormItemAction extends Action
{
    /**
     * {@inheritDoc}
     * @return Response
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     * @throws UniqueFailedException
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
        $formItem->spamFilter = array_filter($body['spamFilter'] ?? []);
        $formItem->isFromAddress = $body['isFromAddress'] ?? false;
        $formItem->isRequired = $body['isRequired'] ?? false;
        $formItem->isSubject = $body['isSubject'] ?? false;

        $formItem->create();

        return $this->respond($formItem->format(), Action::HTTP_CREATED);
    }
}
