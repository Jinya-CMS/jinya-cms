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
    /** @var array */
    private $fields;

    /**
     * MissingFieldsException constructor.
     * @param array $fields
     */
    public function __construct(array $fields)
    {
        parent::__construct();
        $this->fields = $fields;
    }

    /**
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }
}
