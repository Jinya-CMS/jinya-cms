<?php

namespace Jinya\Cms\Utils;

use DateTime;
use Jinya\Cms\Configuration\JinyaConfiguration;
use Psr\Http\Message\ResponseInterface;

abstract class CookieSetter
{
    public static function setCookie(
        ResponseInterface $response,
        string $name,
        string $value,
        DateTime $expires = null,
        bool $httpOnly = true
    ): ResponseInterface {
        $cookie = "$name=$value; Path=/; SameSite=Strict";
        if ($expires) {
            $expiresDate = date(DATE_COOKIE, $expires->getTimestamp());
            $cookie .= "; Expires=$expiresDate";
        }

        if (JinyaConfiguration::getConfiguration()->get('env', 'app', 'prod') === 'prod') {
            $cookie .= '; Secure';
        }

        if ($httpOnly) {
            $cookie .= '; HttpOnly';
        }

        return $response->withAddedHeader('Set-Cookie', $cookie);
    }
}
