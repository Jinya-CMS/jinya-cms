<?php

namespace Jinya\Cms\Database\Converter;

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
            return ['en' => $input];
        }
    }

    /**
     * @throws JsonException
     */
    public function to(mixed $input): string|false
    {
        return json_encode($input, JSON_THROW_ON_ERROR);
    }
}
