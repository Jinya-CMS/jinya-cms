<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 04.11.2017
 * Time: 00:34
 */

namespace Jinya\Components\DataTables;

class DatatablesOrderModel
{
    /** @var string */
    private $column;

    /** @var string */
    private $dir;

    /**
     * @return string
     */
    public function getColumn(): string
    {
        return $this->column;
    }

    /**
     * @param string $column
     */
    public function setColumn(string $column)
    {
        $this->column = $column;
    }

    /**
     * @return string
     */
    public function getDir(): string
    {
        return $this->dir;
    }

    /**
     * @param string $dir
     */
    public function setDir(string $dir)
    {
        $this->dir = $dir;
    }
}
