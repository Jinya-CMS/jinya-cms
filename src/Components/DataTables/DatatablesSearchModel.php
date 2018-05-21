<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 04.11.2017
 * Time: 00:32
 */

namespace Jinya\Components\DataTables;


class DatatablesSearchModel
{
    /** @var bool */
    private $regex;
    /** @var string */
    private $value;

    /**
     * @return bool
     */
    public function isRegex(): bool
    {
        return $this->regex;
    }

    /**
     * @param bool $regex
     */
    public function setRegex(bool $regex)
    {
        $this->regex = $regex;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue(string $value)
    {
        $this->value = $value;
    }
}