<?php

namespace App\Web\Actions\Authentication;

use App\Database\Artist;
use App\Web\Actions\Action;
use App\Web\Exceptions\MissingFieldsException;
use App\Web\Middleware\AuthenticationMiddleware;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpForbiddenException;

class ChangePasswordAction extends Action
{
    /**
     * @return Response
     * @throws MissingFieldsException
     * @throws JsonException
     * @throws HttpForbiddenException
     */
    protected function action(): Response
    {
        $body = $this->request->getParsedBody();
        $password = $body['password'];
        $oldPassword = $body['old_password'];
        $token = $body['token'];
        /** @var Artist $currentArtist */
        $currentArtist = $this->request->getAttribute(AuthenticationMiddleware::LOGGED_IN_ARTIST);

        if ($token) {
            if ($password) {
                if (!$currentArtist->changePassword($token, $password)) {
                    throw new HttpForbiddenException($this->request, 'Token is invalid');
                }
                return $this->noContent();
            }

            throw new MissingFieldsException($this->request, ['password']);
        }

        $newToken = $currentArtist->generatePasswordChangeToken($oldPassword);

        if ($newToken === null) {
            throw new HttpForbiddenException($this->request, 'Old password invalid');
        }

        return $this->respond([
            'url' => '/api/account/password',
            'token' => $newToken,
        ]);
    }
}