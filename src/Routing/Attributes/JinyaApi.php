<?php

namespace App\Routing\Attributes;

use App\Web\Attributes\Authenticated;
use Attribute;

#[Attribute(flags: Attribute::TARGET_CLASS)]
class JinyaApi
{
    public function __construct(
        public readonly bool $createEnabled = true,
        public readonly string $createRole = Authenticated::WRITER,
        public readonly bool $readEnabled = true,
        public readonly string $readRole = Authenticated::READER,
        public readonly bool $updateEnabled = true,
        public readonly string $updateRole = Authenticated::WRITER,
        public readonly bool $deleteEnabled = true,
        public readonly string $deleteRole = Authenticated::WRITER,
    )
    {
    }
}