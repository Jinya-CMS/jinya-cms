<?php

namespace App\Web\Actions\Form\Items;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Form;
use App\Database\FormItem;
use App\Web\Actions\Action;
use App\Web\Exceptions\NoResultException;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Action to create a form item
 */
class CreateFormItemAction extends Action
{
    /**
     * Creates a new form item with the values from the body
     *
     * @return Response
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     * @throws UniqueFailedException
     * @throws \Jinya\PDOx\Exceptions\NoResultException
     */
    protected function action(): Response
    {
        $id = $this->args['id'];
        $form = Form::findById($id);

        if (!$form) {
            throw new NoResultException($this->request, 'Form not found');
        }

        $formItem = new FormItem();
        $formItem->formId = $form->getIdAsInt();
        $formItem->label = $this->body['label'];
        $formItem->placeholder = $this->body['placeholder'] ?? '';
        $formItem->position = $this->body['position'];
        $formItem->helpText = $this->body['helpText'] ?? '';
        $formItem->type = $this->body['type'] ?? 'text';
        $formItem->options = $this->body['options'] ?? [];
        $formItem->spamFilter = array_filter($this->body['spamFilter'] ?? []);
        $formItem->isFromAddress = $this->body['isFromAddress'] ?? false;
        $formItem->isRequired = $this->body['isRequired'] ?? false;
        $formItem->isSubject = $this->body['isSubject'] ?? false;

        $formItem->create();

        return $this->respond($formItem->format(), Action::HTTP_CREATED);
    }
}
