<?php

namespace App\OpenApiGeneration\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class OpenApiParameter
{
    public const IN_PATH = 'path';
    public const IN_HEADER = 'header';
    public const IN_QUERY = 'query';
    public const TYPE_STRING = 'string';
    public const TYPE_EMAIL = 'email';
    public const TYPE_DATE_TIME = 'date-time';
    public const TYPE_ARRAY = 'array';
    public const TYPE_INTEGER = 'integer';
    public const TYPE_BOOLEAN = 'boolean';

    public function __construct(
        public string $name,
        public bool $required,
        public string $type = self::TYPE_STRING,
        public string $in = self::IN_PATH,
        public array $enumValues = [],
    ) {
    }
}