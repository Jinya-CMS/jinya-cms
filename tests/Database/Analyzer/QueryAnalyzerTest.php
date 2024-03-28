<?php

namespace Jinya\Tests\Database\Analyzer;

use App\Database\Analyzer\QueryAnalyzer;
use PHPUnit\Framework\TestCase;

class QueryAnalyzerTest extends TestCase
{
    private QueryAnalyzer $analyzer;

    public function setUp(): void
    {
        $this->analyzer = new QueryAnalyzer();
    }

    public function testGetQueryType(): void
    {
        $statements = $this->analyzer->getStatements('SELECT * FROM users');
        $queryType = $this->analyzer->getQueryType($statements[0]);
        self::assertEquals('SELECT', $queryType);
    }

    public function testGetStatements(): void
    {
        $statements = $this->analyzer->getStatements('SELECT * FROM users');
        self::assertIsArray($statements);
        self::assertCount(1, $statements);
    }
}
