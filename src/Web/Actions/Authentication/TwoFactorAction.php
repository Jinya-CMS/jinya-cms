<?php

namespace App\Web\Actions\Authentication;

use App\Database\Artist;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Mailing\Types\TwoFactorMail;
use App\Web\Actions\Action;
use App\Web\Attributes\JinyaAction;
use App\Web\Attributes\RequiredFields;
use App\Web\Exceptions\BadCredentialsException;
use JetBrains\PhpStorm\Pure;
use JsonException;
use PHPMailer\PHPMailer\Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

#[JinyaAction('/api/2fa', JinyaAction::POST)]
#[RequiredFields(['username', 'password'])]
class TwoFactorAction extends Action
{

    private TwoFactorMail $twoFactorMail;

    /**
     * TwoFactorAction constructor.
     * @param LoggerInterface $logger
     * @param TwoFactorMail $twoFactorMail
     */
    #[Pure] public function __construct(LoggerInterface $logger, TwoFactorMail $twoFactorMail)
    {
        parent::__construct($logger);
        $this->twoFactorMail = $twoFactorMail;
    }

    /**
     * @inheritDoc
     * @return Response
     * @throws JsonException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     * @throws BadCredentialsException
     * @throws Exception
     */
    protected function action(): Response
    {
        $body = $this->request->getParsedBody();
        $artist = Artist::findByEmail($body['username']);
        if ($artist !== null && $artist->loginBlockedUntil !== null && $artist->loginBlockedUntil->getTimestamp(
            ) >= time()) {
            throw new BadCredentialsException($this->request, 'Your account is currently locked');
        }

        if ($artist !== null && $artist->validatePassword($body['password'])) {
            $artist->setTwoFactorCode();
            $artist->update();

            $this->twoFactorMail->sendMail($artist->email, $artist->artistName, $artist->twoFactorToken);

            return $this->respond([], Action::HTTP_NO_CONTENT);
        }

        if ($artist !== null) {
            $artist->twoFactorToken = null;
            if (!$artist->validatePassword($body['password'])) {
                $artist->registerFailedLogin();
            }

            $artist->update();
        }

        return $this->respond([], Action::HTTP_UNAUTHORIZED);
    }
}