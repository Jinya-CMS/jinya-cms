<?php

namespace App\Web\Actions\Message;

use App\Database\Exceptions\UniqueFailedException;
use App\Database\Message;
use App\Web\Actions\Action;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class MarkMessageAsSpamAction extends Action
{

    /**
     * @inheritDoc
     * @throws JsonException
     * @throws UniqueFailedException
     * @throws NoResultException
     */
    protected function action(): Response
    {
        $messageId = $this->args['messageId'];
        $message = Message::findById($messageId);
        if (!$message) {
            throw new NoResultException($this->request, 'Message not found');
        }

        $message->markAsSpam();

        return $this->noContent();
    }
}