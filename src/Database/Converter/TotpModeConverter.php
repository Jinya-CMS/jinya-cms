<?php

namespace Jinya\Cms\Database\Converter;

use Attribute;
use Jinya\Cms\Database\TotpMode;
use Jinya\Database\ValueConverter;

#[Attribute(Attribute::TARGET_PROPERTY)]
class TotpModeConverter implements ValueConverter
{
    /**
     * @inheritDoc
     */
    public function from(mixed $input): TotpMode
    {
        if (is_numeric($input)) {
            return TotpMode::fromInt((int)$input);
        }

        return TotpMode::Email;
    }

    /**
     * @inheritDoc
     * @param TotpMode $input
     */
    public function to(mixed $input): int
    {
        return $input->int();
    }
}
