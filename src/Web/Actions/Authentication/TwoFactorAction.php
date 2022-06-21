<?php

namespace App\Web\Actions\Authentication;

use App\Database\Artist;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Mailing\Types\TwoFactorMail;
use App\Web\Actions\Action;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;
use PHPMailer\PHPMailer\Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

/**
 * Action for two-factor login
 */
class TwoFactorAction extends Action
{
    /** @var TwoFactorMail Two-factor mail factory */
    private TwoFactorMail $twoFactorMail;

    /**
     * TwoFactorAction constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->twoFactorMail = new TwoFactorMail();
    }

    /**
     * Executes the two factor action, the password and email are validated and the two-factor code is send if the values match
     *
     * @return Response
     * @throws Exception
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     * @throws NoResultException
     * @throws Throwable
     */
    protected function action(): Response
    {
        $artist = Artist::findByEmail($this->body['username']);
        if ($artist !== null && $artist->validatePassword($this->body['password'])) {
            $artist->setTwoFactorCode();
            $artist->update();

            $this->twoFactorMail->sendMail($artist->email, $artist->artistName, $artist->twoFactorToken);

            return $this->respond([], Action::HTTP_NO_CONTENT);
        }

        if ($artist !== null) {
            $artist->twoFactorToken = null;
            $artist->update();
        }

        return $this->respond([], Action::HTTP_UNAUTHORIZED);
    }
}