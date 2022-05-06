<?php

namespace App\Web\Exceptions;

use App\Web\Actions\Action;
use Slim\Exception\HttpSpecializedException;

/**
 *
 */
class BadCredentialsException extends HttpSpecializedException
{
    /**
     * @var int
     */
    protected $code = Action::HTTP_FORBIDDEN;
}
