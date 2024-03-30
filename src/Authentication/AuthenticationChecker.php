<?php

namespace Jinya\Cms\Authentication;

use Jinya\Cms\Database\ApiKey;
use Jinya\Cms\Database\Artist;
use Jinya\Cms\Web\Exceptions\ApiKeyInvalidException;
use Jinya\Cms\Web\Exceptions\MissingPermissionsException;
use DateInterval;
use DateTime;
use Psr\Http\Message\ServerRequestInterface as Request;
use Throwable;

/**
 * Helper class to check whether users are allowed to execute the request
 */
class AuthenticationChecker
{
    /**
     * This method checks if the requested role is valid for the user currently logged in.
     * If the artist is logged in and has the given role, it is returned otherwise an exception is thrown.
     *
     * @param Request $request The current request
     * @param string|null $role The role to check for
     * @return Artist The artist containing the given role
     * @throws MissingPermissionsException
     * @throws ApiKeyInvalidException
     */
    public static function checkRequestForUser(Request $request, string|null $role): Artist
    {
        $apiKeyHeader = $request->getHeaderLine('JinyaApiKey');
        $apiKey = ApiKey::findByApiKey($apiKeyHeader);
        if (!$apiKey) {
            throw new ApiKeyInvalidException('Api key invalid');
        }

        $validSince = $apiKey->validSince;
        $expireAfterSeconds = getenv('JINYA_API_KEY_EXPIRY') ?: '86400';
        $validTimeSpan = new DateInterval("PT{$expireAfterSeconds}S");

        if ($validSince->add($validTimeSpan)->getTimestamp() < time()) {
            $apiKey->delete();
            throw new ApiKeyInvalidException('Api key invalid');
        }

        $apiKey->validSince = new DateTime();
        $artist = $apiKey->getArtist();
        if ($artist === null || !$artist->enabled) {
            $apiKey->delete();
            throw new ApiKeyInvalidException('Api key invalid');
        }

        try {
            $apiKey->update();
        } catch (Throwable $exception) {
            throw new ApiKeyInvalidException('Api key invalid', previous: $exception);
        }

        if (!empty($role)) {
            $cascadedRole = match ($role) {
                ROLE_READER => ROLE_WRITER,
                default => '',
            };
            if (!(in_array($role, $artist->roles ?: [], true) || in_array($cascadedRole, $artist->roles ?: [], true))) {
                throw new MissingPermissionsException('Not enough permissions', $role);
            }
        }

        return $artist;
    }
}
