<?php

namespace App\Web\Middleware;

use App\Database\Artist;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpForbiddenException;

class RoleMiddleware implements MiddlewareInterface
{

    public const ROLE_WRITER = 'ROLE_WRITER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    private string $role;

    /**
     * RoleMiddleware constructor.
     * @param string $role
     */
    public function __construct(string $role)
    {
        $this->role = $role;
    }

    /**
     * @inheritDoc
     * @throws HttpForbiddenException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var Artist $artist */
        $artist = $request->getAttribute(AuthenticationMiddleware::LOGGED_IN_ARTIST);
        if (in_array($this->role, $artist->roles, true)) {
            throw new HttpForbiddenException($request, 'Not enough permissions');
        }

        return $handler->handle($request);
    }
}