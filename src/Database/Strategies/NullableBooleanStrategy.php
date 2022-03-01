<?php

namespace App\Database\Strategies;

use Laminas\Hydrator\Exception\InvalidArgumentException;
use Laminas\Hydrator\Strategy\StrategyInterface;

class NullableBooleanStrategy implements StrategyInterface
{
    /**
     * Converts the given value so that it can be extracted by the hydrator.
     *
     * @param ?bool $value The original value.
     * @return ?int Returns the value that should be extracted.
     * @throws InvalidArgumentException
     */
    public function extract($value, ?object $object = null)
    {
        if ($value === true) {
            return 1;
        }
        if ($value === false) {
            return 0;
        }

        return null;
    }

    /**
     * Converts the given value so that it can be hydrated by the hydrator.
     *
     * @param bool|int|string $value The original value.
     * @return bool Returns the value that should be hydrated.
     * @throws InvalidArgumentException
     */
    public function hydrate($value, ?array $data = null)
    {
        if (is_bool($value)) {
            return $value;
        }

        if ($value === 1) {
            return true;
        }

        if ($value === 0) {
            return false;
        }

        return null;
    }
}