<?php

namespace Jinya\Components\Cache;

use JsonSerializable;

class CacheState implements JsonSerializable
{
    /** @var float */
    private $usedMemory;

    /** @var float */
    private $freeMemory;

    /** @var integer */
    private $count;

    /**
     * @return float
     */
    public function getUsedMemory(): float
    {
        return $this->usedMemory;
    }

    /**
     * @param float $usedMemory
     */
    public function setUsedMemory(float $usedMemory): void
    {
        $this->usedMemory = $usedMemory;
    }

    /**
     * @return float
     */
    public function getFreeMemory(): float
    {
        return $this->freeMemory;
    }

    /**
     * @param float $freeMemory
     */
    public function setFreeMemory(float $freeMemory): void
    {
        $this->freeMemory = $freeMemory;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @param int $count
     */
    public function setCount(int $count): void
    {
        $this->count = $count;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [
            'count' => $this->count,
            'freeMemory' => $this->freeMemory,
            'usedMemory' => $this->usedMemory,
        ];
    }
}