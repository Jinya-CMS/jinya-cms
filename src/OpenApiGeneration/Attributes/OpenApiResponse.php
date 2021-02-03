<?php

namespace App\OpenApiGeneration\Attributes;

use App\Web\Actions\Action;
use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class OpenApiResponse
{
    public const FAKER_USER_AGENT = 'userAgent';
    public const FAKER_ISO8601 = 'iso8601';
    public const FAKER_IPV4 = 'ipv4';
    public const FAKER_EMAIL = 'safeEmail';
    public const FAKER_SHA1 = 'sha1';
    public const EXCEPTION_SCHEMA = [
        'success' => ['type' => 'boolean'],
        'error' => [
            'type' => 'object',
            'properties' => [
                'message' => ['type' => 'string'],
                'type' => ['type' => 'string'],
            ],
        ],
    ];
    public const INVALID_API_KEY = [
        'success' => false,
        'error' => [
            'message' => 'Api key invalid',
            'type' => 'HttpForbiddenException',
        ],
    ];
    public const NOT_FOUND = [
        'success' => false,
        'error' => [
            'message' => 'Entity not found',
            'type' => 'HttpNotFoundException',
        ],
    ];
    public const FAKER_NAME = 'name';
    public const FAKER_PARAGRAPH = 'paragraph';
    public const FAKER_WORD = 'word';
    public const FAKER_PASSWORD = 'password';
    public const FAKER_USERNAME = 'userName';
    public const FAKER_NUMERIFY = 'numerify';
    public const FAKER_MIMETYPE = 'mimeType';
    public const MODIFICATION_EXAMPLE = [
        'by' => [
            'artistName' => OpenApiResponse::FAKER_USERNAME,
            'email' => OpenApiResponse::FAKER_EMAIL,
            'profilePicture' => OpenApiResponse::FAKER_SHA1,
        ],
        'at' => OpenApiResponse::FAKER_ISO8601,
    ];
    public const FAKER_URL = 'url';

    public function __construct(
        public string $description,
        public array $example = [],
        public string $exampleName = '',
        public int $statusCode = Action::HTTP_OK,
        public ?string $ref = null,
        public array $schema = [],
        public bool $map = false,
    ) {
    }
}