<?php

namespace App\Database\Strategies;

use Laminas\Hydrator\Strategy\StrategyInterface;

class PhpSerializeStrategy implements StrategyInterface
{

    /**
     * @inheritDoc
     */
    public function extract($value, ?object $object = null)
    {
        return serialize($value);
    }

    /**
     * @inheritDoc
     */
    public function hydrate($value, ?array $data)
    {
        return unserialize($value, ['allowed_classes' => []]);
    }
}