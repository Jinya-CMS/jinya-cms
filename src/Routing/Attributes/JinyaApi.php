<?php

namespace App\Routing\Attributes;

use App\Web\Middleware\RoleMiddleware;
use Attribute;

/**
 *
 */
#[Attribute(flags: Attribute::TARGET_CLASS)]
class JinyaApi
{
    public function __construct(
        public readonly bool $createEnabled = true,
        public readonly string $createRole = RoleMiddleware::ROLE_WRITER,
        public readonly bool $readEnabled = true,
        public readonly string $readRole = RoleMiddleware::ROLE_READER,
        public readonly bool $updateEnabled = true,
        public readonly string $updateRole = RoleMiddleware::ROLE_WRITER,
        public readonly bool $deleteEnabled = true,
        public readonly string $deleteRole = RoleMiddleware::ROLE_WRITER,
    )
    {
    }
}