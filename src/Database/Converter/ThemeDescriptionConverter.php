<?php

namespace App\Database\Converter;

use Attribute;
use Jinya\Database\ValueConverter;
use JsonException;

#[Attribute(Attribute::TARGET_PROPERTY)]
class ThemeDescriptionConverter implements ValueConverter
{
    public function from(mixed $input): mixed
    {
        try {
            return json_decode($input, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return ['en' => $value];
        }
    }

    public function to(mixed $input): mixed
    {
        return json_encode($input, JSON_THROW_ON_ERROR);
    }
}
