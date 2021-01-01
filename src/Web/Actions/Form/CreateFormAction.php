<?php

namespace App\Web\Actions\Form;

use App\Database\Exceptions\UniqueFailedException;
use App\Database\Form;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Attributes\RequiredFields;
use App\Web\Exceptions\ConflictException;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/form', JinyaAction::POST)]
#[Authenticated(role: Authenticated::WRITER)]
#[RequiredFields(['title', 'toAddress'])]
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
        $form->description = $body['description'] ?? '';
        $form->toAddress = $body['toAddress'];

        try {
            $form->create();
        } catch (UniqueFailedException) {
            throw new ConflictException($this->request, 'Title already used');
        }

        return $this->respond($form->format(), Action::HTTP_CREATED);
    }
}