<?php

namespace App\Database\Converter;

use Attribute;
use Jinya\Database\ValueConverter;

#[Attribute(Attribute::TARGET_PROPERTY)]
class PhpSerializerConverter implements ValueConverter
{
    public function from(mixed $input): mixed
    {
        return unserialize($input, ['allowed_classes' => []]);
    }

    public function to(mixed $input): mixed
    {
        return serialize($input);
    }
}
