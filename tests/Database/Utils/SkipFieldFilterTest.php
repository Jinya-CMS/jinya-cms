<?php

namespace Database\Utils;

use App\Database\Utils\SkipFieldFilter;
use PHPUnit\Framework\TestCase;

class SkipFieldFilterTest extends TestCase
{

    public function test__construct(): void
    {
        $filter = new SkipFieldFilter([]);
        $this->assertNotNull($filter);
    }

    public function testFilterSkip(): void
    {
        $filter = new SkipFieldFilter(['skip']);
        $this->assertFalse($filter->filter('skip', null));
    }

    public function testFilterDontSkip(): void
    {
        $filter = new SkipFieldFilter(['skip']);
        $this->assertTrue($filter->filter('test', null));
    }
}
