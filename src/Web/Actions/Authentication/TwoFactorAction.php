<?php

namespace App\Web\Actions\Authentication;

use App\Database\Artist;
use App\Mailing\Types\TwoFactorMail;
use App\Web\Actions\Action;
use App\Web\Exceptions\BadCredentialsException;
use Exception;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class TwoFactorAction extends Action
{

    private TwoFactorMail $twoFactorMail;

    /**
     * TwoFactorAction constructor.
     * @param LoggerInterface $logger
     * @param TwoFactorMail $twoFactorMail
     */
    public function __construct(LoggerInterface $logger, TwoFactorMail $twoFactorMail)
    {
        parent::__construct($logger);
        $this->twoFactorMail = $twoFactorMail;
    }

    /**
     * @inheritDoc
     * @return Response
     * @throws JsonException
     * @throws \App\Database\Exceptions\UniqueFailedException
     * @throws \PHPMailer\PHPMailer\Exception
     */
    protected function action(): Response
    {
        $body = $this->request->getParsedBody();
        $artist = Artist::findByEmail($body['username']);
        if ($artist !== null && $artist->validatePassword($body['password'])) {
            $artist->setTwoFactorCode();
            $artist->update();

            $this->twoFactorMail->sendMail($artist->email, $artist->artistName, $artist->twoFactorToken);

            return $this->respond([], Action::HTTP_NO_CONTENT);
        }

        $artist->twoFactorToken = null;
        $artist->update();

        return $this->noContent();
    }
}