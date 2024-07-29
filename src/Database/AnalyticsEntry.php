<?php

namespace Jinya\Cms\Database;

use DateTime;
use Jinya\Cms\Analytics\DeviceType;
use Jinya\Cms\Analytics\EntityType;
use Jinya\Cms\Database\Converter\BooleanConverter;
use Jinya\Cms\Database\Converter\DeviceTypeConverter;
use Jinya\Cms\Database\Converter\EntityTypeConverter;
use Jinya\Database\Attributes\Column;
use Jinya\Database\Attributes\Id;
use Jinya\Database\Attributes\Table;
use Jinya\Database\Converters\DateConverter;
use Jinya\Database\Entity;
use Jinya\Database\EntityTrait;
use JsonSerializable;

#[Table('analytics')]
class AnalyticsEntry extends Entity implements JsonSerializable
{
    use EntityTrait;

    #[Id]
    #[Column(autogenerated: true)]
    public int $id;

    #[Column(sqlName: 'route')]
    public ?string $route;

    #[Column(sqlName: 'timestamp', autogenerated: true)]
    #[DateConverter('Y-m-d')]
    public DateTime $timestamp;

    #[Column(sqlName: 'unique_visit')]
    #[BooleanConverter]
    public bool $uniqueVisit;

    #[Column(sqlName: 'operating_system')]
    public ?string $operatingSystem;

    #[Column(sqlName: 'operating_system_version')]
    public ?string $operatingSystemVersion;

    #[Column(sqlName: 'browser')]
    public ?string $browser;

    #[Column(sqlName: 'browser_version')]
    public ?string $browserVersion;

    #[Column(sqlName: 'device_type')]
    #[DeviceTypeConverter]
    public ?DeviceType $deviceType;

    #[Column(sqlName: 'entity_id')]
    public ?int $entityId;

    #[Column(sqlName: 'entity_type')]
    #[EntityTypeConverter]
    public ?EntityType $entityType;

    #[Column(sqlName: 'language')]
    public ?string $language;

    #[Column(sqlName: 'country')]
    public ?string $country;

    #[Column(sqlName: 'device')]
    public ?string $device;

    #[Column(sqlName: 'brand')]
    public ?string $brand;

    #[Column(sqlName: 'user_agent')]
    public ?string $userAgent;

    #[Column(sqlName: 'referer')]
    public ?string $referer;

    #[Column(sqlName: 'status')]
    public int $status = 200;

    /**
     * @param string|null $interval
     * @param EntityType|null $entityType
     * @param int|null $id
     * @param bool $uniqueOnly
     * @return array{visits: integer, group: string}[]
     */
    public static function getPastInterval(
        ?string $interval = null,
        ?EntityType $entityType = null,
        ?int $id = null,
        bool $uniqueOnly = false
    ): array {
        $select = self::getQueryBuilder()
            ->newSelect()
            ->cols(['count(*) as visits', 'timestamp as group'])
            ->from(self::getTableName())
            ->groupBy(['timestamp']);

        if ($interval !== null) {
            $select = $select->where("timestamp >= subdate(current_date, interval $interval)");
        }

        if ($uniqueOnly) {
            $select = $select->where('unique_visit = true');
        }

        if ($entityType !== null && $id !== null) {
            $select = $select->where(
                'entity_type = :type AND entity_id = :id',
                ['type' => $entityType->int(), 'id' => $id]
            );
        }

        /** @var array{visits: integer, group: string}[] $data */
        $data = self::executeQuery($select);

        return $data;
    }

    /**
     * @param string $group
     * @param string|null $interval
     * @param EntityType|null $entityType
     * @param int|null $id
     * @param bool $uniqueOnly
     * @return array{visits: integer, group: string}[]
     */
    public static function getPastIntervalGrouped(
        string $group,
        ?string $interval = null,
        ?EntityType $entityType = null,
        ?int $id = null,
        bool $uniqueOnly = false,
    ): array {
        $groupEscaped = self::getPDO()->quote($group);
        $groupColumns = match ($group) {
            'os' => ['operating_system'],
            'os-version' => ['operating_system', 'operating_system_version'],
            'browser-version' => ['browser', 'browser_version'],
            'type' => ['device_type'],
            'date' => ['timestamp'],
            default => [$group],
        };
        $groupColumnsImploded = implode(', ', $groupColumns);
        if (array_key_exists(1, $groupColumns)) {
            $groupColumnsForName = "concat_ws(' ', $groupColumns[0], $groupColumns[1])";
        } elseif (count($groupColumns) === 1) {
            $groupColumnsForName = $groupColumns[0];
        } else {
            $groupColumnsForName = '';
        }
        $select = self::getQueryBuilder()
            ->newSelect()
            ->cols(["count(*) as visits", "$groupColumnsForName as group"])
            ->from(self::getTableName())
            ->groupBy($groupColumns);

        if ($interval !== null) {
            $select = $select->where("timestamp >= subdate(current_date, interval $interval)");
        }

        if ($uniqueOnly) {
            $select = $select->where('unique_visit = true');
        }

        if ($entityType !== null && $id !== null) {
            $select = $select->where(
                'entity_type = :type AND entity_id = :id',
                ['type' => $entityType->int(), 'id' => $id]
            );
        }

        /** @var array{visits: integer, group: string}[] $data */
        $data = self::executeQuery($select);

        return $data;
    }

    /**
     * @param int $months
     * @param EntityType $type
     * @param int $id
     * @return array{visits: integer, group: string}[]
     */
    public static function getPastNMonthsForEntity(int $months, EntityType $type, int $id): array
    {
        return self::getPastInterval("$months month", $type, $id);
    }

    public static function getTotalPastInterval(?string $interval): int
    {
        $select = self::getQueryBuilder()
            ->newSelect()
            ->cols(['COUNT(*) as visits'])
            ->from(self::getTableName())
            ->where('unique_visit = true');

        if ($interval !== null) {
            $select = $select->where("timestamp >= subdate(current_date, interval $interval)");
        }

        /** @var array{visits: int}[] $result */
        $result = self::executeQuery($select);

        return $result[0]['visits'];
    }

    /**
     * @return array<string, string|int|bool|null>
     */
    public function jsonSerialize(): array
    {
        return $this->format();
    }

    /**
     * @return array<string, string|int|bool|null>
     */
    public function format(): array
    {
        return [
            'id' => $this->id,
            'route' => $this->route,
            'timestamp' => $this->timestamp->format(DATE_ATOM),
            'uniqueVisit' => $this->uniqueVisit,
            'operatingSystem' => $this->operatingSystem,
            'operatingSystemVersion' => $this->operatingSystemVersion,
            'browser' => $this->browser,
            'browserVersion' => $this->browserVersion,
            'deviceType' => $this->deviceType?->string(),
            'entityId' => $this->entityId,
            'entityType' => $this->entityType?->string(),
            'language' => $this->language,
            'device' => $this->device,
            'brand' => $this->brand,
            'country' => $this->country,
            'userAgent' => $this->userAgent,
        ];
    }

    /**
     * @return BlogPost|BlogCategory|Form|Gallery|ModernPage|ClassicPage|Artist|null
     */
    public function getRelatedEntity(): BlogPost|BlogCategory|Form|Gallery|ModernPage|ClassicPage|Artist|null
    {
        return match ($this->entityType) {
            EntityType::BlogPost => BlogPost::findById($this->entityId ?? -1),
            EntityType::BlogCategory => BlogCategory::findById($this->entityId ?? -1),
            EntityType::Form => Form::findById($this->entityId ?? -1),
            EntityType::Gallery => Gallery::findById($this->entityId ?? -1),
            EntityType::ModernPage => ModernPage::findById($this->entityId ?? -1),
            EntityType::ClassicPage => ClassicPage::findById($this->entityId ?? -1),
            EntityType::Artist => Artist::findById($this->entityId ?? -1),
            default => null,
        };
    }

    /**
     * @return MenuItem|null
     */
    public function getRelatedMenuItem(): MenuItem|null
    {
        return MenuItem::findByRoute($this->route);
    }
}
