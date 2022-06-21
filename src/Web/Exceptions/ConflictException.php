<?php

namespace App\Web\Exceptions;

use App\Web\Actions\Action;
use Slim\Exception\HttpSpecializedException;

/**
 * This exception is thrown when a conflict arises, a common example is failed unique and foreign key constraints
 */
class ConflictException extends HttpSpecializedException
{
    /** @var int The return code for failed auth, currently 409 */
    protected $code = Action::HTTP_CONFLICT;
}
