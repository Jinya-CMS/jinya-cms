<?php

namespace Jinya\Tests\Routing\Attributes;

use App\Routing\Attributes\JinyaApiField;
use PHPUnit\Framework\TestCase;

class JinyaApiFieldTest extends TestCase
{

    public function test__construct(): void
    {
        $attribute = new JinyaApiField();
        self::assertFalse($attribute->ignore);
        self::assertFalse($attribute->required);
    }
}
