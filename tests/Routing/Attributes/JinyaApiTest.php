<?php

namespace Jinya\Tests\Routing\Attributes;

use App\Authentication\AuthenticationChecker;
use App\Routing\Attributes\JinyaApi;
use PHPUnit\Framework\TestCase;

class JinyaApiTest extends TestCase
{
    public function test__construct(): void
    {
        $attribute = new JinyaApi();
        self::assertEquals(true, $attribute->createEnabled);
        self::assertEquals(AuthenticationChecker::ROLE_WRITER, $attribute->createRole);
        self::assertEquals(true, $attribute->readEnabled);
        self::assertEquals(AuthenticationChecker::ROLE_READER, $attribute->readRole);
        self::assertEquals(true, $attribute->updateEnabled);
        self::assertEquals(AuthenticationChecker::ROLE_WRITER, $attribute->updateRole);
        self::assertEquals(true, $attribute->deleteEnabled);
        self::assertEquals(AuthenticationChecker::ROLE_WRITER, $attribute->deleteRole);
    }
}
