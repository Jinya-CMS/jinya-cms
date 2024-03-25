<?php

namespace App\Database\Converter;

use Attribute;
use Jinya\Database\ValueConverter;

#[Attribute(Attribute::TARGET_PROPERTY)]
class JsonConverter implements ValueConverter
{
    public function from(mixed $input): mixed
    {
        return json_decode($input, false, 512, JSON_THROW_ON_ERROR);
    }

    public function to(mixed $input): mixed
    {
        return json_encode($input, JSON_THROW_ON_ERROR);
    }
}
