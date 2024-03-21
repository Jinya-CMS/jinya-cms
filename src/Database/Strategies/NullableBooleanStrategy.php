<?php

namespace App\Database\Strategies;

use Laminas\Hydrator\Exception\InvalidArgumentException;
use Laminas\Hydrator\Strategy\StrategyInterface;

/**
 * Laminas hydrator strategy to convert nullable booleans between PHP and MySQL
 */
class NullableBooleanStrategy implements StrategyInterface
{
    /**
     * Converts the given value so that it can be extracted by the hydrator.
     *
     * @param ?bool $value The original value.
     * @return ?int Returns the value that should be extracted.
     * @throws InvalidArgumentException
     */
    public function extract($value, ?object $object = null): ?int
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
     * @param bool|int|string|null $value The original value.
     * @param array<string>|null $data
     * @return bool|null Returns the value that should be hydrated.
     */
    public function hydrate($value, ?array $data = null): ?bool
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
