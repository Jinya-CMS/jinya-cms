<?php

namespace App\Database\Converter;

use Attribute;
use Jinya\Database\ValueConverter;

#[Attribute(Attribute::TARGET_PROPERTY)]
class NullableBooleanConverter implements ValueConverter
{
    public function from(mixed $input): mixed
    {
        if (is_bool($input)) {
            return $input;
        }

        if ($input === 1) {
            return true;
        }

        if ($input === 0) {
            return false;
        }

        return null;
    }

    public function to(mixed $input): mixed
    {
        if ($input === true) {
            return 1;
        }
        if ($input === false) {
            return 0;
        }

        return null;
    }
}
