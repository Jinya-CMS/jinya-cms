<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 17.12.2017
 * Time: 20:25.
 */

namespace Jinya\Exceptions;

use Throwable;

class EmptySlugException extends \Exception
{
    public function __construct(string $message = 'The slug cannot be empty', int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
