<?php

namespace Jinya\Cms\Database\Converter;

use Attribute;
use Jinya\Cms\Analytics\DeviceType;
use Jinya\Database\ValueConverter;

#[Attribute(Attribute::TARGET_PROPERTY)]
class DeviceTypeConverter implements ValueConverter
{
    /**
     * @inheritDoc
     */
    public function from(mixed $input): ?DeviceType
    {
        if (is_numeric($input)) {
            return DeviceType::fromInt((int)$input);
        }

        return null;
    }

    /**
     * @inheritDoc
     * @param DeviceType|null $input
     */
    public function to(mixed $input): ?int
    {
        return $input?->int();
    }
}
