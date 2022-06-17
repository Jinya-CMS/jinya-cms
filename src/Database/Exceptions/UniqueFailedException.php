<?php

namespace App\Database\Exceptions;

use Jinya\PDOx\Exceptions\InvalidQueryException;

/**
 * This exception is thrown when a unique constraint fails
 */
class UniqueFailedException extends InvalidQueryException
{
}
