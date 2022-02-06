<?php

namespace App\Web\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Authenticated
{
    public const READER = 'ROLE_READER';
    public const WRITER = 'ROLE_WRITER';
    public const ADMIN = 'ROLE_ADMIN';


    /**
     * Route constructor.
     * @param string $role
     */
    public function __construct(
        public string $role = '',
    )
    {
    }
}