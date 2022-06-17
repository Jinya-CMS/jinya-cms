<?php

namespace App\Database\Utils;

/**
 * Common interface for all entities that implement the format method
 */
interface FormattableEntityInterface
{
    /**
     * Formats the entity into an array
     * @phpstan-ignore-next-line
     */
    public function format(): array;
}
