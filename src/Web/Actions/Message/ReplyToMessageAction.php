<?php

namespace App\Web\Actions\Message;

use App\Database\Artist;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Message;
use App\Mailing\Types\FormAnswerEmail;
use App\Web\Actions\Action;
use App\Web\Exceptions\NoResultException;
use App\Web\Middleware\AuthenticationMiddleware;
use JsonException;
use PHPMailer\PHPMailer\Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class ReplyToMessageAction extends Action
{

    private FormAnswerEmail $formAnswerEmail;

    /**
     * ReplyToMessageAction constructor.
     * @param LoggerInterface $logger
     * @param FormAnswerEmail $formAnswerEmail
     */
    public function __construct(LoggerInterface $logger, FormAnswerEmail $formAnswerEmail)
    {
        parent::__construct($logger);
        $this->formAnswerEmail = $formAnswerEmail;
    }

    /**
     * @inheritDoc
     * @throws Exception
     * @throws UniqueFailedException
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

        $body = $this->request->getParsedBody();
        $answer = $body['answer'];
        $subject = $body['subject'];
        /** @var Artist $loggedInArtist */
        $loggedInArtist = $this->request->getAttribute(AuthenticationMiddleware::LOGGED_IN_ARTIST);
        $this->formAnswerEmail->sendMail($loggedInArtist->email, $loggedInArtist->artistName, $message->fromAddress, $subject,
            $answer);

        $message->answer = $answer;
        $message->update();

        return $this->noContent();
    }
}