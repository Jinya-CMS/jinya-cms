<?php

namespace Jinya\Tests\Database\Analyzer;

use App\Database\Analyzer\DatabaseAnalyzer;
use App\Database\Analyzer\VariablesType;
use App\Utils\AppSettingsInitializer;
use PHPUnit\Framework\TestCase;

class DatabaseAnalyzerTest extends TestCase
{
    protected function setUp(): void
    {
        AppSettingsInitializer::loadDotEnv();
    }

    public function testGetVariablesGlobal(): void
    {
        $variables = DatabaseAnalyzer::getVariables(VariablesType::Global);
        $this->assertIsArray($variables);
    }

    public function testGetVariablesSession(): void
    {
        $variables = DatabaseAnalyzer::getVariables(VariablesType::Session);
        $this->assertIsArray($variables);
    }

    public function testGetVariablesLocal(): void
    {
        $variables = DatabaseAnalyzer::getVariables(VariablesType::Local);
        $this->assertIsArray($variables);
    }

    public function testGetServerInfo(): void
    {
        $serverInfo = DatabaseAnalyzer::getServerInfo();
        $this->assertArrayHasKey('version', $serverInfo, 'The key should exist');
        $this->assertArrayHasKey('comment', $serverInfo, 'The key should exist');
        $this->assertArrayHasKey('compileMachine', $serverInfo, 'The key should exist');
        $this->assertArrayHasKey('compileOs', $serverInfo, 'The key should exist');
    }

    public function testGetTables(): void
    {
        $tables = DatabaseAnalyzer::getTables();
        $this->assertIsArray($tables);
    }
}
