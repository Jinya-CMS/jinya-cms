<?php

namespace App\OpenApiGeneration\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class OpenApiField
{
    public const FORMAT_EMAIL = 'email';
    public const FORMAT_DATE_TIME = 'date-time';
    public const CHANGED_BY_STRUCTURE = [
        'by' => [
            'type' => 'object',
            'properties' => [
                'artistName' => ['type' => 'string'],
                'email' => ['type' => 'string', 'format' => self::FORMAT_EMAIL],
                'profilePicture' => ['type' => 'string'],
            ],
        ],
        'at' => ['type' => 'string', 'format' => self::FORMAT_DATE_TIME],
    ];

    public function __construct(
        public ?bool $required = null,
        public ?array $enumValues = null,
        public ?string $format = null,
        public mixed $defaultValue = null,
        public bool $object = false,
        public ?string $objectRef = null,
        public bool $array = false,
        public string $arrayRef = '',
        public string $arrayType = 'string',
        public array $structure = [],
        public ?string $name = null,
    ) {
    }
}