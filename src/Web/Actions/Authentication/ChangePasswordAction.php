<?php

namespace App\Web\Actions\Authentication;

use App\Database\Artist;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Web\Actions\Action;
use App\Web\Exceptions\MissingFieldsException;
use App\Web\Middleware\AuthorizationMiddleware;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpForbiddenException;

/**
 * Action to change the password
 */
class ChangePasswordAction extends Action
{
    /**
     * Changes the password for the current user
     *
     * @return Response
     * @throws ForeignKeyFailedException
     * @throws HttpForbiddenException
     * @throws InvalidQueryException
     * @throws MissingFieldsException
     * @throws UniqueFailedException
     */
    protected function action(): Response
    {
        $password = $this->body['password'];
        $oldPassword = $this->body['oldPassword'];
        /** @var Artist $currentArtist */
        $currentArtist = $this->request->getAttribute(AuthorizationMiddleware::LOGGED_IN_ARTIST);

        if (!$currentArtist->changePassword($oldPassword, $password)) {
            throw new HttpForbiddenException($this->request, 'Old password is invalid');
        }

        return $this->noContent();
    }
}
