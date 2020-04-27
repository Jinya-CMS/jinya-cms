<?php

namespace App\Web\Middleware;

use App\Authentication\CurrentUser;
use App\Database\Artist;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Exception\HttpForbiddenException;

class AuthenticationMiddleware implements MiddlewareInterface
{

    public const LOGGED_IN_ARTIST = 'logged_in_artist';

    /**
     * @inheritDoc
     * @throws HttpForbiddenException
     */
    public function process(Request $request, RequestHandler $handler): ResponseInterface
    {
        $apiKeyHeader = $request->getHeaderLine('JinyaApiKey');
        $artist = Artist::findByApiKey($apiKeyHeader);
        if (!$artist) {
            throw new HttpForbiddenException($request, 'Api key invalid');
        }

        CurrentUser::$currentUser = $artist;

        return $handler->handle($request->withAttribute(self::LOGGED_IN_ARTIST, $artist));
    }
}