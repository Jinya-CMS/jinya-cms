<?php

namespace App\OpenApiGeneration\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class OpenApiRequestBody
{
    public const FAKER_USER_AGENT = 'userAgent';
    public const FAKER_ISO8601 = 'iso8601';
    public const FAKER_IPV4 = 'ipv4';
    public const FAKER_EMAIL = 'safeEmail';
    public const FAKER_SHA1 = 'sha1';

    public function __construct(public array $schema, public string $exampleName, public array $example)
    {
    }
}