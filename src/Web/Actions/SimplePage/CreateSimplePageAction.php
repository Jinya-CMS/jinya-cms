<?php

namespace App\Web\Actions\SimplePage;

use App\Database\Exceptions\UniqueFailedException;
use App\Database\SimplePage;
use App\Web\Actions\Action;
use App\Web\Exceptions\ConflictException;
use Exception;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class CreateSimplePageAction extends Action
{

    /**
     * @inheritDoc
     * @throws ConflictException
     * @throws JsonException
     * @throws Exception
     */
    protected function action(): Response
    {
        $body = $this->request->getParsedBody();

        $page = new SimplePage();
        $page->title = $body['title'];
        $page->content = $body['content'];

        try {
            $page->create();
        } catch (UniqueFailedException $exception) {
            throw new ConflictException($this->request, 'Title is used');
        }

        return $this->respond($page->format(), Action::HTTP_CREATED);
    }
}