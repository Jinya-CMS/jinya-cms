<?php

namespace App\Authentication;

use App\Database\ApiKey;
use App\Database\Artist;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use DateInterval;
use DateTime;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpForbiddenException;
use Throwable;

/**
 * Helper class to check whether users are allowed to execute the request
 */
class AuthenticationChecker
{
    /** @var string Users in this role are allowed to see content but not edit it */
    public const ROLE_READER = 'ROLE_READER';
    /** @var string Users in this role are allowed to see and edit content but not administer Jinya CMS */
    public const ROLE_WRITER = 'ROLE_WRITER';
    /** @var string Users in this role are allowed to see and edit content and to administer Jinya CMS */
    public const ROLE_ADMIN = 'ROLE_ADMIN';

    /**
     * This method checks if the requested role is valid for the user currently logged in. If the artist is logged in and has the given role it is returned, otherwise an exception is thrown.
     *
     * @param Request $request The current request
     * @param string|null $role The role to check for
     * @return Artist The artist containing the given role
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     * @throws UniqueFailedException
     */
    public static function checkRequestForUser(Request $request, string|null $role): Artist
    {
        $apiKeyHeader = $request->getHeaderLine('JinyaApiKey');
        $apiKey = ApiKey::findByApiKey($apiKeyHeader);
        if (!$apiKey) {
            throw new HttpForbiddenException($request, 'Api key invalid');
        }

        $validSince = $apiKey->validSince;
        $expireAfterSeconds = getenv('JINYA_API_KEY_EXPIRY') ?: '86400';
        $validTimeSpan = new DateInterval("PT{$expireAfterSeconds}S");

        if ($validSince->add($validTimeSpan)->getTimestamp() < time()) {
            $apiKey->delete();
            throw new HttpForbiddenException($request, 'Api key invalid');
        }

        $apiKey->validSince = new DateTime();
        $artist = $apiKey->getArtist();
        if ($artist === null || !$artist->enabled) {
            $apiKey->delete();
            throw new HttpForbiddenException($request, 'Api key invalid');
        }

        try {
            $apiKey->update();
        } catch (Throwable $exception) {
            throw new HttpForbiddenException($request, 'Api key invalid', $exception);
        }

        if (!empty($role)) {
            $cascadedRole = match ($role) {
                self::ROLE_READER => self::ROLE_WRITER,
                default => '',
            };
            if (!(in_array($role, $artist->roles ?: [], true) || in_array($cascadedRole, $artist->roles ?: [], true))) {
                throw new HttpForbiddenException($request, 'Not enough permissions');
            }
        }

        return $artist;
    }
}