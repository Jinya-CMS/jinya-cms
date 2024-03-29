<?php

namespace Jinya\Tests\Database\Analyzer;

use App\Database\Analyzer\DatabaseAnalyzer;
use App\Database\Analyzer\VariablesType;
use PHPUnit\Framework\TestCase;

class DatabaseAnalyzerTest extends TestCase
{
    public function testGetVariablesGlobal(): void
    {
        $variables = DatabaseAnalyzer::getVariables(VariablesType::Global);
        self::assertIsArray($variables);
    }

    public function testGetVariablesSession(): void
    {
        $variables = DatabaseAnalyzer::getVariables(VariablesType::Session);
        self::assertIsArray($variables);
    }

    public function testGetVariablesLocal(): void
    {
        $variables = DatabaseAnalyzer::getVariables(VariablesType::Local);
        self::assertIsArray($variables);
    }

    public function testGetServerInfo(): void
    {
        $serverInfo = DatabaseAnalyzer::getServerInfo();
        self::assertArrayHasKey('version', $serverInfo, 'The key should exist');
        self::assertArrayHasKey('comment', $serverInfo, 'The key should exist');
        self::assertArrayHasKey('compileMachine', $serverInfo, 'The key should exist');
        self::assertArrayHasKey('compileOs', $serverInfo, 'The key should exist');
    }

    public function testGetTables(): void
    {
        $tables = DatabaseAnalyzer::getTables();
        self::assertIsArray($tables);
    }
}
