<?php

namespace Jinya\Cms\Database\Converter;

use Attribute;
use Jinya\Cms\Analytics\EntityType;
use Jinya\Database\ValueConverter;

#[Attribute(Attribute::TARGET_PROPERTY)]
class EntityTypeConverter implements ValueConverter
{
    /**
     * @inheritDoc
     */
    public function from(mixed $input): ?EntityType
    {
        if (is_numeric($input)) {
            return EntityType::fromInt((int)$input);
        }

        return null;
    }

    /**
     * @inheritDoc
     * @param EntityType|null $input
     */
    public function to(mixed $input): ?int
    {
        return $input?->int();
    }
}
