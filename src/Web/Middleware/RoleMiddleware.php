<?php

namespace App\Web\Middleware;

use App\Database\Artist;
use App\Web\Attributes\Authenticated;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpForbiddenException;

class RoleMiddleware implements MiddlewareInterface
{

    public const ROLE_READER = 'ROLE_READER';
    public const ROLE_WRITER = 'ROLE_WRITER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';

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
        $cascadedRole = match ($this->role) {
            Authenticated::READER => Authenticated::WRITER,
            default => '',
        };
        if (!(in_array($this->role, $artist->roles, true) || in_array($cascadedRole, $artist->roles, true))) {
            throw new HttpForbiddenException($request, 'Not enough permissions');
        }

        return $handler->handle($request);
    }
}