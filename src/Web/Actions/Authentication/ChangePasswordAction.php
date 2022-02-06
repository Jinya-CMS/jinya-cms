<?php

namespace App\Web\Actions\Authentication;

use App\Database\Artist;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Attributes\RequiredFields;
use App\Web\Exceptions\MissingFieldsException;
use App\Web\Middleware\AuthenticationMiddleware;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpForbiddenException;

#[JinyaAction('/api/account/password', JinyaAction::POST)]
#[Authenticated]
#[RequiredFields(['oldPassword', 'password'])]
class ChangePasswordAction extends Action
{
    /**
     * @return Response
     * @throws ForeignKeyFailedException
     * @throws HttpForbiddenException
     * @throws InvalidQueryException
     * @throws MissingFieldsException
     * @throws UniqueFailedException
     */
    protected function action(): Response
    {
        $body = $this->request->getParsedBody();
        $password = $body['password'];
        $oldPassword = $body['oldPassword'];
        /** @var Artist $currentArtist */
        $currentArtist = $this->request->getAttribute(AuthenticationMiddleware::LOGGED_IN_ARTIST);

        if ($password) {
            if (!$currentArtist->changePassword($oldPassword, $password)) {
                throw new HttpForbiddenException($this->request, 'Old password is invalid');
            }

            return $this->noContent();
        }

        throw new MissingFieldsException($this->request, ['password']);
    }
}
