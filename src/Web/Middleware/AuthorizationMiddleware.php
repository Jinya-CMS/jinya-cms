<?php

namespace Jinya\Cms\Web\Middleware;

use DateInterval;
use Jinya\Cms\Authentication\AuthenticationChecker;
use Jinya\Cms\Authentication\CurrentUser;
use Jinya\Cms\Configuration\JinyaConfiguration;
use Jinya\Cms\Utils\CookieSetter;
use Jinya\Cms\Web\Exceptions\ApiKeyInvalidException;
use Jinya\Cms\Web\Exceptions\MissingPermissionsException;
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

        $response = $handler->handle($request);

        $apiKey = AuthenticationChecker::getApiKeyFromRequest($request);
        $apiKeyExpires = JinyaConfiguration::getConfiguration()->get('api_key_expiry', 'jinya', 86400);

        return CookieSetter::setCookie(
            $response,
            AuthenticationChecker::AUTHENTICATION_COOKIE_NAME,
            $apiKey?->apiKey,
            $apiKey?->validSince->add(new DateInterval("PT{$apiKeyExpires}S"))
        );
    }
}
