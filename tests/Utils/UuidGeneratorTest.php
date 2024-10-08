<?php

namespace Jinya\Cms\Utils;

use Exception;
use PHPUnit\Framework\TestCase;

class UuidGeneratorTest extends TestCase
{
    public function testGenerateV4(): void
    {
        try {
            $uuid = UuidGenerator::generateV4();
            self::assertMatchesRegularExpression(
                '/^[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$/',
                $uuid,
                'UUID matches format'
            );
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }
    }
}
