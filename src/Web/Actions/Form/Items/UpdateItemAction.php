<?php

namespace App\Web\Actions\Form\Items;

use App\Database\Exceptions\UniqueFailedException;
use App\Database\FormItem;
use App\Web\Actions\Action;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpNotFoundException;

class UpdateItemAction extends Action
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
        $formItem = FormItem::findByPosition($this->args['id'], $this->args['position']);
        if (!$formItem) {
            throw new HttpNotFoundException($this->request, 'Form item not found');
        }

        if (isset($body['label'])) {
            $formItem->label = $body['label'];
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

        $formItem->update();

        if ($body['newPosition']) {
            $formItem->move($body['newPosition']);
        }

        return $this->respond($formItem->format(), Action::HTTP_CREATED);
    }
}