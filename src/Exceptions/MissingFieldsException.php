<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 17.02.2018
 * Time: 19:11
 */

namespace Jinya\Exceptions;

use Exception;

class MissingFieldsException extends Exception
{
    private array $fields;

    /**
     * MissingFieldsException constructor.
     */
    public function __construct(array $fields)
    {
        parent::__construct();
        $this->fields = $fields;
    }

    public function getFields(): array
    {
        return $this->fields;
    }
}
