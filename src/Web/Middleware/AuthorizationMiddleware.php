<?php

namespace App\Web\Middleware;

use App\Authentication\AuthenticationChecker;
use App\Authentication\CurrentUser;
use App\Web\Exceptions\ApiKeyInvalidException;
use App\Web\Exceptions\MissingPermissionsException;
use JsonException;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

/**
 * The authorization middleware checks the authorization and sets the currently logged-in user
 */
readonly class AuthorizationMiddleware implements MiddlewareInterface
{
    /** @var string Constant for the currently logged-in user */
    public const LOGGED_IN_ARTIST = 'logged_in_artist';

    /**
     * Creates a new authorization middleware and initializes it with the needed role
     *
     * @param string|null $role
     */
    public function __construct(private string|null $role = null)
    {
    }

    /**
     * Processes the middleware, during the processing the logged-in user will be checked against the role,
     * and the current user will be set to the user from the request
     * @throws JsonException
     */
    public function process(Request $request, RequestHandler $handler): ResponseInterface
    {
        try {
            $artist = AuthenticationChecker::checkRequestForUser($request, $this->role);
        } catch (ApiKeyInvalidException) {
            return new Response(401, ['Content-Type' => 'application/json'], json_encode([
                'success' => false,
                'error' => [
                    'message' => 'API key is invalid',
                    'type' => 'invalid-api-key'
                ]
            ], JSON_THROW_ON_ERROR));
        } catch (MissingPermissionsException $e) {
            return new Response(403, ['Content-Type' => 'application/json'], json_encode([
                'success' => false,
                'error' => [
                    'message' => 'You do not have enough permissions, please request the role ' . $e->role,
                    'type' => 'missing-permissions'
                ]
            ], JSON_THROW_ON_ERROR));
        }

        CurrentUser::$currentUser = $artist;

        return $handler->handle($request);
    }
}
