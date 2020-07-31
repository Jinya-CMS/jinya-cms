<?php

namespace App\Web\Actions\Message;

use App\Database\Message;
use App\Web\Actions\Action;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class GetMessageAction extends Action
{

    /**
     * @inheritDoc
     * @throws NoResultException
     * @throws JsonException
     */
    protected function action(): Response
    {
        $messageId = $this->args['messageId'];
        $message = Message::findById($messageId);
        if (!$message) {
            throw new NoResultException($this->request, 'Message not found');
        }

        return $this->respond($message->format());
    }
}