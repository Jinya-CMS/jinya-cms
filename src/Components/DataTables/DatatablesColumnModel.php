<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 04.11.2017
 * Time: 00:33
 */

namespace Jinya\Components\DataTables;

class DatatablesColumnModel
{
    /** @var string */
    private $data;

    /** @var string */
    private $name;

    /** @var bool */
    private $orderable;

    /** @var bool */
    private $searchable;

    /** @var DatatablesSearchModel */
    private $search;

    /**
     * @return string
     */
    public function getData(): string
    {
        return $this->data;
    }

    /**
     * @param string $data
     */
    public function setData(string $data)
    {
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return bool
     */
    public function isOrderable(): bool
    {
        return $this->orderable;
    }

    /**
     * @param bool $orderable
     */
    public function setOrderable(bool $orderable)
    {
        $this->orderable = $orderable;
    }

    /**
     * @return bool
     */
    public function isSearchable(): bool
    {
        return $this->searchable;
    }

    /**
     * @param bool $searchable
     */
    public function setSearchable(bool $searchable)
    {
        $this->searchable = $searchable;
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
}
