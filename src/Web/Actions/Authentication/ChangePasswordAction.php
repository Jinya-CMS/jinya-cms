<?php

namespace App\Web\Actions\Authentication;

use App\Database\Artist;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiRequestBody;
use App\OpenApiGeneration\Attributes\OpenApiRequestExample;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Attributes\RequiredFields;
use App\Web\Exceptions\MissingFieldsException;
use App\Web\Middleware\AuthenticationMiddleware;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpForbiddenException;

#[JinyaAction('/api/account/password', JinyaAction::POST)]
#[Authenticated]
#[RequiredFields(['oldPassword', 'password'])]
#[OpenApiRequest('This action changes the password')]
#[OpenApiRequestBody([
    'oldPassword' => ['type' => 'string', 'format' => 'password'],
    'password' => ['type' => 'string', 'format' => 'password'],
])]
#[OpenApiRequestExample('Change password request', [
    'oldPassword' => OpenApiResponse::FAKER_PASSWORD,
    'password' => OpenApiResponse::FAKER_PASSWORD,
])]
#[OpenApiResponse('Successfully changed password', statusCode: Action::HTTP_NO_CONTENT)]
#[OpenApiResponse('Invalid device code', example: [
    'success' => false,
    'error' => [
        'message' => 'Unknown device',
        'type' => 'UnknownDeviceException',
    ],
], exampleName: 'Invalid device code', statusCode: Action::HTTP_UNAUTHORIZED, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
class ChangePasswordAction extends Action
{
    /**
     * @throws HttpForbiddenException
     * @throws JsonException
     * @throws MissingFieldsException
     * @throws UniqueFailedException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
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
