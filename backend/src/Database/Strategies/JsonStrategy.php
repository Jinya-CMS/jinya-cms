<?php

namespace App\Database\Strategies;

use Laminas\Hydrator\Strategy\StrategyInterface;

class JsonStrategy implements StrategyInterface
{

    /**
     * @inheritDoc
     */
    public function extract($value, ?object $object = null)
    {
        return json_encode($value, JSON_THROW_ON_ERROR);
    }

    /**
     * @inheritDoc
     */
    public function hydrate($value, ?array $data)
    {
        return json_decode($value, true, 512, JSON_THROW_ON_ERROR);
    }
}