<?php

namespace App\Database\Utils;

interface FormattableEntityInterface
{
    /**
     * Formats the entity into an array
     *
     * @return array
     */
    public function format(): array;
}