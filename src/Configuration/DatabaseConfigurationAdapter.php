<?php

namespace Jinya\Cms\Configuration;

use Countable;
use Jinya\Configuration\Adapter\AdapterInterface;
use Jinya\Database\Entity;
use Throwable;

class DatabaseConfigurationAdapter implements AdapterInterface
{
    private const TYPE_STRING = 0;
    private const TYPE_INTEGER = 1;
    private const TYPE_BOOLEAN = 2;

    /**
     * @inheritDoc
     */
    public function get(string $key, ?string $group = null, bool|int|string|null $default = null): string|bool|int|null
    {
        try {
            $group = $group ?? '';
            $query = Entity::getQueryBuilder()
                ->newSelect()
                ->cols(['type', 'value'])
                ->from('jinya_configuration')
                ->where('`key` = ? AND `group` = ?', [$key, $group]);
            /** @var array<string, mixed>[] $result */
            $result = Entity::executeQuery($query);
            if (empty($result)) {
                return $default;
            }

            return match ($result[0]['type']) {
                self::TYPE_INTEGER => (int)$result[0]['value'],
                self::TYPE_BOOLEAN => (bool)$result[0]['value'],
                default => (string)$result[0]['value'],
            };
        } catch (Throwable $e) {
            return null;
        }
    }

    /**
     * @inheritDoc
     */
    public function getAll(?string $group = null): array
    {
        try {
            $group = $group ?? '';
            $query = Entity::getQueryBuilder()
                ->newSelect()
                ->cols(['type', 'value', '`key`'])
                ->from('jinya_configuration')
                ->where('`group` = ?', [$group]);
            /** @var array<string, mixed>[] $result */
            $result = Entity::executeQuery($query);
            if (empty($result)) {
                return [];
            }

            $keys = array_map(static fn (array $item) => $item['key'], $result);
            $values = array_map(static fn (array $item) => match ($item['type']) {
                self::TYPE_INTEGER => (int)$item['value'],
                self::TYPE_BOOLEAN => (bool)$item['value'],
                default => (string)$item['value'],
            }, $result);

            return array_combine($keys, $values);
        } catch (Throwable $e) {
            return [];
        }
    }

    /**
     * @inheritDoc
     */
    public function set(string $key, bool|int|string $value, ?string $group = null): void
    {
        $group = $group ?? '';
        $query = Entity::getQueryBuilder()
            ->newSelect()
            ->cols(['type'])
            ->from('jinya_configuration')
            ->where('`key` = ? AND `group` = ?', [$key, $group]);

        /** @var Countable $count */
        $count = Entity::executeQuery($query);
        $row = ['key' => $key, 'group' => $group, 'value' => (string)$value];
        if (is_int($value)) {
            $row['type'] = self::TYPE_INTEGER;
        } elseif (is_bool($value)) {
            $row['type'] = self::TYPE_BOOLEAN;
        } else {
            $row['type'] = self::TYPE_STRING;
        }

        if (count($count) === 0) {
            $query = Entity::getQueryBuilder()
                ->newInsert()
                ->into('jinya_configuration')
                ->addRow($row);
        } else {
            $query = Entity::getQueryBuilder()
                ->newUpdate()
                ->table('jinya_configuration')
                ->set('value', ':value')
                ->set('type', ':type')
                ->where('`key` = :key AND `group` = :group')
                ->bindValues($row);
        }

        Entity::executeQuery($query);
    }

    /**
     * @inheritDoc
     */
    public function delete(string $key, ?string $group = null): void
    {
        $group = $group ?? '';
        $query = Entity::getQueryBuilder()
            ->newDelete()
            ->from('jinya_configuration')
            ->where('`key` = ? AND `group` = ?', [$key, $group]);

        Entity::executeQuery($query);
    }
}
