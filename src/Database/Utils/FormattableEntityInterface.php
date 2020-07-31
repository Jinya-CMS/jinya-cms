<?php

namespace App\Database\Utils;

interface FormattableEntityInterface
{
    public function format(): array;
}