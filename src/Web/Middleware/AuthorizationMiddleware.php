<?php

namespace App\Web\Middleware;

use App\Authentication\AuthenticationChecker;
use App\Authentication\CurrentUser;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Exception\HttpForbiddenException;

/**
 * The authorization middleware checks the authorization and sets the currently logged in user
 */
class AuthorizationMiddleware implements MiddlewareInterface
{
    /** @var string Constant for the currently logged in user */
    public const LOGGED_IN_ARTIST = 'logged_in_artist';

    /**
     * Creates a new authorization middleware and initalizes it with the needed role
     *
     * @param string|null $role
     */
    public function __construct(private readonly string|null $role = null)
    {
    }

    /**
     * Processes the middleware, during the processing the logged in user will be checked against the role and the current user will be set to the user from the request
     *
     * @throws HttpForbiddenException
     * @throws Exception
     */
    public function process(Request $request, RequestHandler $handler): ResponseInterface
    {
        $artist = AuthenticationChecker::checkRequestForUser($request, $this->role);
        CurrentUser::$currentUser = $artist;

        return $handler->handle($request->withAttribute(self::LOGGED_IN_ARTIST, $artist));
    }
}
