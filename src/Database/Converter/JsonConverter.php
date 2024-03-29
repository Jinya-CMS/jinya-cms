<?php

namespace App\Database\Converter;

use Attribute;
use Jinya\Database\ValueConverter;
use JsonException;

#[Attribute(Attribute::TARGET_PROPERTY)]
class JsonConverter implements ValueConverter
{
    /**
     * @throws JsonException
     */
    public function from(mixed $input): mixed
    {
        return json_decode($input, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @throws JsonException
     */
    public function to(mixed $input): string|false
    {
        return json_encode($input, JSON_THROW_ON_ERROR);
    }
}
