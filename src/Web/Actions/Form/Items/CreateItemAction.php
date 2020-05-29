<?php

namespace App\Web\Actions\Form\Items;

use App\Database\Exceptions\UniqueFailedException;
use App\Database\Form;
use App\Database\FormItem;
use App\Web\Actions\Action;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpNotFoundException;

class CreateItemAction extends Action
{

    /**
     * @inheritDoc
     * @throws UniqueFailedException
     * @throws JsonException
     * @throws HttpNotFoundException
     */
    protected function action(): Response
    {
        $body = $this->request->getParsedBody();
        $id = $this->args['id'];
        $form = Form::findById($id);

        if (!$form) {
            throw new HttpNotFoundException($this->request, 'Form not found');
        }

        $formItem = new FormItem();
        $formItem->formId = $form->id;
        $formItem->label = $body['label'];
        $formItem->position = $body['position'];
        $formItem->helpText = $body['helpText'] ?? '';
        $formItem->type = $body['type'] ?? 'text';
        $formItem->options = $body['options'] ?? [];
        $formItem->spamFilter = $body['spamFilter'] ?? [];

        $formItem->create();

        return $this->respond($formItem->format(), Action::HTTP_CREATED);
    }
}