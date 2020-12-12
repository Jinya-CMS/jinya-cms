<?php

namespace App\Web\Actions\Form;

use App\Database\Form;
use App\Web\Actions\Action;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class DeleteFormAction extends Action
{

    /**
     * @inheritDoc
     * @return Response
     * @throws JsonException
     * @throws NoResultException
     * @throws \App\Database\Exceptions\ForeignKeyFailedException
     * @throws \App\Database\Exceptions\InvalidQueryException
     * @throws \App\Database\Exceptions\UniqueFailedException
     */
    protected function action(): Response
    {
        $form = Form::findById($this->args['id']);
        if ($form === null) {
            throw new NoResultException($this->request, 'Form not found');
        }

        $form->delete();

        return $this->noContent();
    }
}