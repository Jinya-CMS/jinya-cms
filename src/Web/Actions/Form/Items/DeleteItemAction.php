<?php

namespace App\Web\Actions\Form\Items;

use App\Database\FormItem;
use App\Web\Actions\Action;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpNotFoundException;

class DeleteItemAction extends Action
{

    /**
     * @inheritDoc
     * @throws HttpNotFoundException
     * @throws JsonException
     */
    protected function action(): Response
    {
        $formItem = FormItem::findByPosition($this->args['id'], $this->args['position']);
        if (!$formItem) {
            throw new HttpNotFoundException($this->request, 'Form item not found');
        }

        $formItem->delete();

        return $this->noContent();
    }
}