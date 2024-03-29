<?php

namespace App\Web\Exceptions;

use Exception;

class MissingPermissionsException extends Exception
{
    public function __construct(string $message, public readonly string|null $role)
    {
        parent::__construct($message);
    }
}
