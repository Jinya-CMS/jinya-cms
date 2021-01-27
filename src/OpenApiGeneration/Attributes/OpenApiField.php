<?php

namespace App\OpenApiGeneration\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class OpenApiField
{
    public const FORMAT_EMAIL = 'email';
    public const FORMAT_DATE_TIME = 'date-time';

    public bool $array = false;
    public string $arrayRef = '';

    public function __construct(
        public ?bool $required = null,
        public ?array $enumValues = null,
        public ?string $format = null,
        public mixed $defaultValue = null,
    ) {
    }
}