<?php

namespace App\Web\Actions\Form;

use App\Database\Exceptions\UniqueFailedException;
use App\Database\Form;
use App\Web\Actions\Action;
use App\Web\Exceptions\ConflictException;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;

class CreateFormAction extends Action
{
    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function action(): Response
    {
        $body = $this->request->getParsedBody();
        $form = new Form();
        $form->title = $body['title'];
        $form->description = $body['description'];
        $form->toAddress = $body['toAddress'];

        try {
            $form->create();
        } catch (UniqueFailedException $exception) {
            throw new ConflictException($this->request, 'Title already used');
        }

        return $this->respond($form->format(), Action::HTTP_CREATED);
    }
}