<?php

namespace Jinya\Cms\Database;

use DateInterval;
use DateTime;
use Faker;
use Jinya\Cms\Analytics\DeviceType;
use Jinya\Cms\Analytics\EntityType;
use Jinya\Cms\Tests\DatabaseAwareTestCase;
use Jinya\Database\Entity;

class AnalyticsEntryTest extends DatabaseAwareTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $faker = Faker\Factory::create();

        for ($i = 0; $i < 999; $i++) {
            $timestamp = $faker->dateTimeBetween('-2 year', 'now');
            $entry = new AnalyticsEntry();
            $entry->uniqueVisit = $faker->boolean(15);
            $entry->route = $faker->url;
            $entry->operatingSystem = $faker->randomElement(['Windows', 'Mac', 'Linux']);
            $entry->operatingSystemVersion = $faker->randomElement(['10', '11', '12']);
            $entry->browser = $faker->randomElement(['Chrome', 'Firefox', 'Safari']);
            $entry->browserVersion = $faker->randomElement(['90', '91', '92']);
            $entry->deviceType = $faker->randomElement(DeviceType::cases());
            $entry->entityId = $faker->numberBetween(1, 100);
            $entry->entityType = $faker->randomElement(EntityType::cases());
            $entry->language = $faker->languageCode;
            $entry->device = $faker->randomElement(['iPhone', 'iPad', 'Samsung Galaxy']);
            $entry->brand = $faker->randomElement(['Apple', 'Samsung', 'Google']);
            $entry->userAgent = $faker->userAgent;
            $entry->referer = $faker->url;
            $entry->country = $faker->countryCode;

            $entry->create();
            Entity::getPDO()->exec(
                "update analytics set timestamp = '{$timestamp->format('Y-m-d')}' where id = {$entry->entityId}"
            );
        }

        $timestamp = (new DateTime('now'))->sub(new DateInterval('P5Y'));
        $entry = new AnalyticsEntry();
        $entry->uniqueVisit = $faker->boolean(15);
        $entry->route = $faker->url;
        $entry->operatingSystem = $faker->randomElement(['Windows', 'Mac', 'Linux']);
        $entry->operatingSystemVersion = $faker->randomElement(['10', '11', '12']);
        $entry->browser = $faker->randomElement(['Chrome', 'Firefox', 'Safari']);
        $entry->browserVersion = $faker->randomElement(['90', '91', '92']);
        $entry->deviceType = $faker->randomElement(DeviceType::cases());
        $entry->entityId = 101;
        $entry->entityType = EntityType::BlogPost;
        $entry->language = $faker->languageCode;
        $entry->device = $faker->randomElement(['iPhone', 'iPad', 'Samsung Galaxy']);
        $entry->brand = $faker->randomElement(['Apple', 'Samsung', 'Google']);
        $entry->userAgent = $faker->userAgent;
        $entry->referer = $faker->url;
        $entry->country = $faker->countryCode;

        $entry->create();

        Entity::getPDO()->exec(
            "update analytics set timestamp = '{$timestamp->format('Y-m-d')}' where id = {$entry->entityId}"
        );
    }

    public function testGetPastInterval(): void
    {
        $result = iterator_to_array(AnalyticsEntry::getPastInterval());
        self::assertNotEmpty($result);

        $result = iterator_to_array(AnalyticsEntry::getPastInterval(interval: '-1 day'));
        self::assertEmpty($result);

        $result = iterator_to_array(AnalyticsEntry::getPastInterval(interval: '24 month'));
        self::assertNotEmpty($result);

        $result = iterator_to_array(AnalyticsEntry::getPastInterval(uniqueOnly: true));
        self::assertLessThan(1000, count($result));

        $result = iterator_to_array(AnalyticsEntry::getPastInterval(entityType: EntityType::BlogPost, id: 101));
        self::assertLessThan(1000, count($result));
    }

    public function testGetPastIntervalGrouped(): void
    {
        $result = iterator_to_array(AnalyticsEntry::getPastIntervalGrouped('os'));
        self::assertLessThanOrEqual(9, count($result));
        self::assertGreaterThanOrEqual(1, count($result));

        $result = iterator_to_array(AnalyticsEntry::getPastIntervalGrouped('os', interval: '-1 day'));
        self::assertEmpty($result);

        $result = iterator_to_array(AnalyticsEntry::getPastIntervalGrouped('os', interval: '24 month'));
        self::assertLessThanOrEqual(9, count($result));
        self::assertGreaterThanOrEqual(1, count($result));

        $result = iterator_to_array(AnalyticsEntry::getPastIntervalGrouped('os', uniqueOnly: true));
        self::assertLessThanOrEqual(9, count($result));
        self::assertGreaterThanOrEqual(1, count($result));

        $result = iterator_to_array(
            AnalyticsEntry::getPastIntervalGrouped('os', entityType: EntityType::BlogPost, id: 101)
        );
        self::assertLessThanOrEqual(9, count($result));
        self::assertGreaterThanOrEqual(1, count($result));
    }

    public function testFormat(): void
    {
        $all = AnalyticsEntry::findAll();
        $all->next();
        $first = $all->current();

        self::assertEquals([
            'id' => $first->id,
            'route' => $first->route,
            'timestamp' => $first->timestamp->format(DATE_ATOM),
            'uniqueVisit' => $first->uniqueVisit,
            'operatingSystem' => $first->operatingSystem,
            'operatingSystemVersion' => $first->operatingSystemVersion,
            'browser' => $first->browser,
            'browserVersion' => $first->browserVersion,
            'deviceType' => $first->deviceType?->string(),
            'entityId' => $first->entityId,
            'entityType' => $first->entityType?->string(),
            'language' => $first->language,
            'device' => $first->device,
            'brand' => $first->brand,
            'userAgent' => $first->userAgent,
            'country' => $first->country
        ], $first->format());
    }

    public function testGetRelatedMenuItem(): void
    {
        $menu = new Menu();
        $menu->name = 'Test';
        $menu->create();

        $menu->replaceItems([
            [
                'route' => 'test',
                'title' => 'Testtitle',
            ]
        ]);

        $faker = Faker\Factory::create();

        $entry = new AnalyticsEntry();
        $entry->uniqueVisit = $faker->boolean(15);
        $entry->timestamp = $faker->dateTimeBetween('-1 year', 'now');
        $entry->route = 'test';
        $entry->operatingSystem = $faker->randomElement(['Windows', 'Mac', 'Linux']);
        $entry->operatingSystemVersion = $faker->randomElement(['10', '11', '12']);
        $entry->browser = $faker->randomElement(['Chrome', 'Firefox', 'Safari']);
        $entry->browserVersion = $faker->randomElement(['90', '91', '92']);
        $entry->deviceType = $faker->randomElement(DeviceType::cases());
        $entry->entityId = $faker->numberBetween(1, 100);
        $entry->entityType = $faker->randomElement(EntityType::cases());
        $entry->language = $faker->languageCode;
        $entry->device = $faker->randomElement(['iPhone', 'iPad', 'Samsung Galaxy']);
        $entry->brand = $faker->randomElement(['Apple', 'Samsung', 'Google']);
        $entry->userAgent = $faker->userAgent;
        $entry->referer = $faker->url;

        $entry->create();

        $menuItem = $entry->getRelatedMenuItem();

        self::assertNotNull($menuItem);

        $menu->replaceItems([]);

        $menuItem = $entry->getRelatedMenuItem();

        self::assertNull($menuItem);
    }
}
