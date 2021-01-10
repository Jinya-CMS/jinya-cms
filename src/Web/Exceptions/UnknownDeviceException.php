<?php

namespace App\Web\Exceptions;

use App\Web\Actions\Action;
use Slim\Exception\HttpSpecializedException;

class UnknownDeviceException extends HttpSpecializedException
{
    protected $code = Action::HTTP_UNAUTHORIZED;
}
