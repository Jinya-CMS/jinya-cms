<?php

namespace Jinya\Cms\Configuration;

use Jinya\Cms\Tests\DatabaseAwareTestCase;

class DatabaseConfigurationAdapterTest extends DatabaseAwareTestCase
{
    public function testDelete(): void
    {
        $adapter = new DatabaseConfigurationAdapter();
        $adapter->set('testString', 'test', 'group');
        $adapter->set('testString', 'test');

        $testString = $adapter->get('testString', 'group');
        self::assertEquals('test', $testString);

        $testString = $adapter->get('testString');
        self::assertEquals('test', $testString);

        $adapter->delete('testString', 'group');
        $adapter->delete('testString');

        $testString = $adapter->get('testString', 'group');
        self::assertNull($testString);

        $testString = $adapter->get('testString');
        self::assertNull($testString);
    }

    public function testGetNotFound(): void
    {
        $adapter = new DatabaseConfigurationAdapter();

        $result = $adapter->get('non-existing-key');
        self::assertNull($result);

        $result = $adapter->get('non-existing-key', 'non-existing-group');
        self::assertNull($result);

        $result = $adapter->get('non-existing-key', 'non-existing-group', true);
        self::assertTrue($result);
    }

    public function testSetNoGroup(): void
    {
        $adapter = new DatabaseConfigurationAdapter();
        $adapter->set('testString', 'test');
        $adapter->set('testInt', 1);
        $adapter->set('testBool', true);

        $testString = $adapter->get('testString');
        self::assertEquals('test', $testString);

        $testInt = $adapter->get('testInt');
        self::assertEquals(1, $testInt);

        $testBool = $adapter->get('testBool');
        self::assertTrue($testBool);
    }

    public function testSetWithGroup(): void
    {
        $adapter = new DatabaseConfigurationAdapter();
        $adapter->set('testString', 'test', 'group');
        $adapter->set('testInt', 1, 'group');
        $adapter->set('testBool', true, 'group');

        $testString = $adapter->get('testString', 'group');
        self::assertEquals('test', $testString);

        $testInt = $adapter->get('testInt', 'group');
        self::assertEquals(1, $testInt);

        $testBool = $adapter->get('testBool', 'group');
        self::assertTrue($testBool);

        $allInGroup = $adapter->getAll('group');
        self::assertCount(3, $allInGroup);
        self::assertEquals('test', $allInGroup['testString']);
        self::assertEquals(1, $allInGroup['testInt']);
        self::assertTrue($allInGroup['testBool']);
    }

    public function testGetAllEmptyGroup(): void
    {
        $adapter = new DatabaseConfigurationAdapter();

        $emptyResult = $adapter->getAll();
        self::assertEmpty($emptyResult);

        $emptyResult = $adapter->getAll('non-existing-group');
        self::assertEmpty($emptyResult);
    }
}
