<?php

namespace App\Web\Actions\SimplePage;

use App\Database\Exceptions\UniqueFailedException;
use App\Database\SimplePage;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Attributes\RequiredFields;
use App\Web\Exceptions\ConflictException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/page', JinyaAction::POST)]
#[Authenticated(Authenticated::WRITER)]
#[RequiredFields(['title', 'content'])]
class CreateSimplePageAction extends Action
{

    /**
     * @inheritDoc
     * @return Response
     * @throws ConflictException
     * @throws UniqueFailedException
     * @throws \App\Database\Exceptions\ForeignKeyFailedException
     * @throws \App\Database\Exceptions\InvalidQueryException
     */
    protected function action(): Response
    {
        $body = $this->request->getParsedBody();

        $page = new SimplePage();
        $page->title = $body['title'];
        $page->content = $body['content'];

        try {
            $page->create();
        } catch (UniqueFailedException) {
            throw new ConflictException($this->request, 'Title is used');
        }

        return $this->respond($page->format(), Action::HTTP_CREATED);
    }
}