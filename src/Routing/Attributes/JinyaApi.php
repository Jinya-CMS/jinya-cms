<?php

namespace App\Routing\Attributes;

use App\Authentication\AuthenticationChecker;
use Attribute;

/**
 *
 */
#[Attribute(flags: Attribute::TARGET_CLASS)]
class JinyaApi
{
    public function __construct(
        public readonly bool $createEnabled = true,
        public readonly string $createRole = AuthenticationChecker::ROLE_WRITER,
        public readonly bool $readEnabled = true,
        public readonly string $readRole = AuthenticationChecker::ROLE_READER,
        public readonly bool $updateEnabled = true,
        public readonly string $updateRole = AuthenticationChecker::ROLE_WRITER,
        public readonly bool $deleteEnabled = true,
        public readonly string $deleteRole = AuthenticationChecker::ROLE_WRITER,
    )
    {
    }
}