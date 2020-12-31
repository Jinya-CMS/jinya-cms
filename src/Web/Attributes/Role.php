<?php

namespace App\Web\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_FUNCTION | Attribute::IS_REPEATABLE)]
class Role
{
    public const READER = 'ROLE_READER';
    public const WRITER = 'ROLE_WRITER';
    public const ADMIN = 'ROLE_ADMIN';

    private string $role;

    /**
     * Role constructor.
     * @param string $role
     */
    public function __construct(string $role = self::READER)
    {
        $this->role = $role;
    }
}