<?php

namespace App\Database\Exceptions;

use Jinya\PDOx\Exceptions\InvalidQueryException;

class ForeignKeyFailedException extends InvalidQueryException
{
}
