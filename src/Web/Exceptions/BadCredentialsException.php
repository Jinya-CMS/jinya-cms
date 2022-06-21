<?php

namespace App\Web\Exceptions;

use App\Web\Actions\Action;
use Slim\Exception\HttpSpecializedException;

/**
 * This exception is thrown when the credentials are invalid, cause either username and password don't match or the api key is invalid
 */
class BadCredentialsException extends HttpSpecializedException
{
    /** @var int The return code for failed auth, currently 403 */
    protected $code = Action::HTTP_FORBIDDEN;
}
