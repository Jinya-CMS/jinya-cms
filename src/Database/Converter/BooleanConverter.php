<?php

namespace App\Database\Converter;

use Attribute;
use Jinya\Database\ValueConverter;

#[Attribute(Attribute::TARGET_PROPERTY)]
class BooleanConverter implements ValueConverter
{
    public function from(mixed $input): mixed
    {
        if (is_bool($input)) {
            return $input;
        }

        if ($input === 1) {
            return true;
        }

        return false;
    }

    public function to(mixed $input): mixed
    {
        if ($input === true) {
            return 1;
        }

        return 0;
    }
}
