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
 *
 */
class AuthorizationMiddleware implements MiddlewareInterface
{
    public const LOGGED_IN_ARTIST = 'logged_in_artist';

    public function __construct(private readonly string|null $role = null)
    {
    }

    /**
     * {@inheritDoc}
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
