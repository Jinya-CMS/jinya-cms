<?php

namespace App\Web\Actions\Form\Items;

use App\Database\Form;
use App\Web\Actions\Action;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class GetItemsAction extends Action
{

    /**
     * @inheritDoc
     * @throws JsonException
     */
    protected function action(): Response
    {
        $id = $this->args['id'];
        $items = Form::findById($id)->getItems();

        return $this->respond(iterator_to_array($items));
    }
}