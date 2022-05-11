<?php

namespace App\Database\Utils;

/**
 *
 */
interface FormattableEntityInterface
{
    /**
     * Formats the entity into an array
     * @phpstan-ignore-next-line
     */
    public function format(): array;
}
