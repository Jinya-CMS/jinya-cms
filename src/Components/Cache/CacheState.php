<?php

namespace Jinya\Components\Cache;

use JsonSerializable;

class CacheState implements JsonSerializable
{
    private float $usedMemory;

    private float $freeMemory;

    private int $count;

    public function getUsedMemory(): float
    {
        return $this->usedMemory;
    }

    public function setUsedMemory(float $usedMemory): void
    {
        $this->usedMemory = $usedMemory;
    }

    public function getFreeMemory(): float
    {
        return $this->freeMemory;
    }

    public function setFreeMemory(float $freeMemory): void
    {
        $this->freeMemory = $freeMemory;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function setCount(int $count): void
    {
        $this->count = $count;
    }

    /**
     * {@inheritdoc}
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
