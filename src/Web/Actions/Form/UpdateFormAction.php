<?php

namespace App\Web\Actions\Form;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Form;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\ConflictException;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/form/{id}', JinyaAction::PUT)]
#[Authenticated(role: Authenticated::WRITER)]
class UpdateFormAction extends Action
{

    /**
     * @inheritDoc
     * @return Response
     * @throws ConflictException
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
        if ($form === null) {
            throw new NoResultException($this->request, 'Form not found');
        }

        if (isset($body['title'])) {
            $form->title = $body['title'];
        }

        if (isset($body['description'])) {
            $form->description = $body['description'];
        }

        if (isset($body['toAddress'])) {
            $form->toAddress = $body['toAddress'];
        }

        try {
            $form->update();
        } catch (UniqueFailedException) {
            throw new ConflictException($this->request, 'Title already used');
        }

        return $this->noContent();
    }
}