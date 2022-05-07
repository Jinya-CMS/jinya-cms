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

/**
 *
 */
class AuthenticationChecker
{
    public const ROLE_READER = 'ROLE_READER';
    public const ROLE_WRITER = 'ROLE_WRITER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';

    /**
     * @param Request $request
     * @param string $role
     * @return Artist
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
        $validTimeSpan = new DateInterval("PT${expireAfterSeconds}S");

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

        $apiKey->update();

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