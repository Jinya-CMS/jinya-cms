<?php

namespace Jinya\Tests\Database\Utils;

use App\Database\Utils\ThemeHelperEntity;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class ThemeHelperEntityTest extends TestCase
{

    public function testFindByKeyword(): void
    {
        $this->expectException(RuntimeException::class);
        ThemeHelperEntity::findByKeyword('');
    }

    public function testFindById(): void
    {
        $this->expectException(RuntimeException::class);
        ThemeHelperEntity::findById(0);
    }

    public function testFindAll(): void
    {
        $this->expectException(RuntimeException::class);
        ThemeHelperEntity::findAll();
    }
}
