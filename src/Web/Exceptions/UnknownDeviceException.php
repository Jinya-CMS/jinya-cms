<?php

namespace App\Web\Exceptions;

use App\Web\Actions\Action;
use Slim\Exception\HttpSpecializedException;

/**
 * This exception is thrown, when the device code used to bypass two-factor auth is unknown or not valid for the user
 */
class UnknownDeviceException extends HttpSpecializedException
{
    /** @var int The return code for an unknown device, currently 401 */
    protected $code = Action::HTTP_UNAUTHORIZED;
}
