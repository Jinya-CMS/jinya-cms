<?php

namespace App\Database\Exceptions;

use Jinya\PDOx\Exceptions\InvalidQueryException;

/**
 * This exception is thrown when a foreign key constraint fails during deletion or creation
 */
class ForeignKeyFailedException extends InvalidQueryException
{
}
