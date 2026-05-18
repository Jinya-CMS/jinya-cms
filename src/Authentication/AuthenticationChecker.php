<?php

namespace Jinya\Cms\Authentication;

use DateInterval;
use DateTime;
use Jinya\Cms\Configuration\JinyaConfiguration;
use Jinya\Cms\Database\ApiKey;
use Jinya\Cms\Database\Artist;
use Jinya\Cms\Logging\Logger;
use Jinya\Cms\Web\Exceptions\ApiKeyInvalidException;
use Jinya\Cms\Web\Exceptions\MissingPermissionsException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * Helper class to check whether users are allowed to execute the request
 */
class AuthenticationChecker
{
    public const AUTHENTICATION_COOKIE_NAME = 'JinyaApiKey';

    private static ?LoggerInterface $logger = null;

    private static function getLogger(): LoggerInterface
    {
        if (self::$logger) {
            return self::$logger;
        }

        self::$logger = Logger::getLogger();

        return self::$logger;
    }

    /**
     * Retrieves the api key from the request based on all available authorization methods
     *
     * @param Request $request
     * @return ApiKey|null
     */
    public static function getApiKeyFromRequest(Request $request): ?ApiKey
    {
        $authorizationHeader = substr($request->getHeaderLine('Authorization'), strlen('Bearer '));
        $key = $request->getCookieParams()[self::AUTHENTICATION_COOKIE_NAME] ?? $authorizationHeader;

        return ApiKey::findByApiKey($key);
    }

    /**
     * Retrieves the plain api key from the request based on all available authorization methods
     *
     * @param Request $request
     * @return string
     */
    public static function getPlainApiKeyFromRequest(Request $request): string
    {
        $authorizationHeader = substr($request->getHeaderLine('Authorization'), strlen('Bearer '));
        return $request->getCookieParams()[self::AUTHENTICATION_COOKIE_NAME] ?? $authorizationHeader;
    }

    /**
     * This method checks if the requested role is valid for the user currently logged in.
     * If the artist is logged in and has the given role, it is returned, otherwise an exception is thrown.
     *
     * @param Request $request The current request
     * @param string|null $role The role to check for
     * @return Artist The artist containing the given role
     * @throws MissingPermissionsException
     * @throws ApiKeyInvalidException
     */
    public static function checkRequestForUser(Request $request, string|null $role): Artist
    {
        self::getLogger()->debug('Check if api exists in request');
        $apiKey = self::getApiKeyFromRequest($request);

        if (!$apiKey) {
            self::getLogger()->debug('Api key not found');
            throw new ApiKeyInvalidException('Api key invalid');
        }

        $validSince = $apiKey->validSince;
        $expireAfterSeconds = JinyaConfiguration::getConfiguration()->get('api_key_expiry', 'jinya', 86400);
        $validTimeSpan = new DateInterval("PT{$expireAfterSeconds}S");

        self::getLogger()->debug('Check if api key is valid');
        if ($validSince->add($validTimeSpan)->getTimestamp() < time()) {
            self::getLogger()->debug('Api key is invalid, delete it');
            $apiKey->delete();
            throw new ApiKeyInvalidException('Api key invalid');
        }

        $apiKey->validSince = new DateTime();
        $artist = $apiKey->getArtist();
        self::getLogger()->debug('Check if the api keys artist is enabled');
        if ($artist === null || !$artist->enabled) {
            self::getLogger()->debug('Api key is invalid, delete it');
            $apiKey->delete();
            throw new ApiKeyInvalidException('Api key invalid');
        }

        try {
            self::getLogger()->debug('Touch api key and refresh the validation');
            $apiKey->update();
        } catch (Throwable $exception) {
            self::getLogger()->debug('Failed to touch the api key', ['exception' => $exception]);
            throw new ApiKeyInvalidException('Api key invalid', previous: $exception);
        }

        if (!empty($role)) {
            $cascadedRole = match ($role) {
                ROLE_READER => ROLE_WRITER,
                default => '',
            };
            self::getLogger()->debug('Check if artist has required role');
            if (!(in_array($role, $artist->roles ?: [], true) || in_array($cascadedRole, $artist->roles ?: [], true))) {
                self::getLogger()->debug('Artist does not have the required role');
                throw new MissingPermissionsException('Not enough permissions', $role);
            }
        }

        return $artist;
    }
}
