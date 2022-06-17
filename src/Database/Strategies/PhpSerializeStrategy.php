<?php

namespace App\Database\Strategies;

use Laminas\Hydrator\Strategy\StrategyInterface;

/**
 * Laminas hydrator strategy to serialize and deserialize PHP data with serialize method
 */
class PhpSerializeStrategy implements StrategyInterface
{
    /**
     * {@inheritDoc}
     */
    public function extract($value, ?object $object = null)
    {
        return serialize($value);
    }

    /**
     * {@inheritDoc}
     * @param array<string>|null $data
     */
    public function hydrate($value, ?array $data)
    {
        return unserialize($value, ['allowed_classes' => []]);
    }
}
