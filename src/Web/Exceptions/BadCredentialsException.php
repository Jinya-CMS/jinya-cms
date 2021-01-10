<?php

namespace App\Web\Exceptions;

use App\Web\Actions\Action;
use Slim\Exception\HttpSpecializedException;

class BadCredentialsException extends HttpSpecializedException
{
    protected $code = Action::HTTP_FORBIDDEN;
}
