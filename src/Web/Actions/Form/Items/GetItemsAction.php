<?php

namespace App\Web\Actions\Form\Items;

use App\Database\Form;
use App\Web\Actions\Action;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class GetItemsAction extends Action
{

    /**
     * @inheritDoc
     * @throws JsonException
     * @throws NoResultException
     */
    protected function action(): Response
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