<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 04.11.2017
 * Time: 00:28
 */

namespace Jinya\Components\DataTables;


class DatatablesModel
{
    /** @var int */
    private $length;
    /** @var int */
    private $start;
    /** @var mixed */
    private $draw;
    /** @var DatatablesColumnModel[] */
    private $columns;
    /** @var DatatablesOrderModel[] */
    private $order;
    /** @var DatatablesSearchModel */
    private $search;

    /**
     * @return int
     */
    public function getLength(): int
    {
        return $this->length;
    }

    /**
     * @param int $length
     */
    public function setLength(int $length)
    {
        $this->length = $length;
    }

    /**
     * @return int
     */
    public function getStart(): int
    {
        return $this->start;
    }

    /**
     * @param int $start
     */
    public function setStart(int $start)
    {
        $this->start = $start;
    }

    /**
     * @return mixed
     */
    public function getDraw()
    {
        return $this->draw;
    }

    /**
     * @param mixed $draw
     */
    public function setDraw($draw)
    {
        $this->draw = $draw;
    }

    /**
     * @return DatatablesColumnModel[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @param DatatablesColumnModel[] $columns
     */
    public function setColumns(array $columns)
    {
        $this->columns = $columns;
    }

    /**
     * @return DatatablesOrderModel[]
     */
    public function getOrder(): array
    {
        return $this->order;
    }

    /**
     * @param DatatablesOrderModel[] $order
     */
    public function setOrder(array $order)
    {
        $this->order = $order;
    }

    /**
     * @return DatatablesSearchModel
     */
    public function getSearch(): DatatablesSearchModel
    {
        return $this->search;
    }

    /**
     * @param DatatablesSearchModel $search
     */
    public function setSearch(DatatablesSearchModel $search)
    {
        $this->search = $search;
    }

    public function getColumn(string $name)
    {
        $filterResults = array_filter($this->columns, function ($var) use ($name) {
            /** @var $var DatatablesColumnModel */
            return $var->getName() === $name;
        });
        return array_shift($filterResults);
    }
}