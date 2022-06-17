<?php

namespace App\Database\Exceptions;

use Exception;

/**
 * This exception gets thrown when the result is empty, but was excepted to contain data
 */
class EmptyResultException extends Exception
{
}
