<?php

namespace Jinya\Cms\Database;

use Iterator;
use Jinya\Cms\Logging\Logger;
use Jinya\Database\Creatable;
use Jinya\Database\CreatableEntityTrait;
use Jinya\Database\Deletable;
use Jinya\Database\DeletableEntityTrait;
use Jinya\Database\EntityTrait;
use Jinya\Database\Findable;
use Jinya\Database\FindableEntityTrait;
use Jinya\Database\Updatable;
use Jinya\Database\UpdatableEntityTrait;
use JsonSerializable;
use Psr\Log\LoggerInterface;
use Throwable;

abstract class LoggingEntity implements Creatable, Deletable, Findable, Updatable, JsonSerializable
{
    use EntityTrait;
    use DeletableEntityTrait {
        DeletableEntityTrait::delete as traitDelete;
    }
    use CreatableEntityTrait {
        CreatableEntityTrait::create as traitCreate;
    }
    use FindableEntityTrait {
        FindableEntityTrait::findAll as traitFindAll;
        FindableEntityTrait::findById as traitFindById;
        FindableEntityTrait::findByFilters as traitFindByFilters;
        FindableEntityTrait::findRange as traitFindRange;
        FindableEntityTrait::countAll as traitCountAll;
        FindableEntityTrait::countByFilters as traitCountByFilters;
    }
    use UpdatableEntityTrait {
        UpdatableEntityTrait::update as traitUpdate;
    }

    protected LoggerInterface $logger;

    /**
     * @return array<string, array<string, array<string, string|null>|string>|int|string>
     */
    abstract public function format(): array;

    public function __construct()
    {
        $this->logger = Logger::getLogger();
    }

    private function getId(): mixed
    {
        $idColumn = self::getIdProperty();
        return $this->{$idColumn['name']};
    }

    /**
     * @inheritDoc
     */
    public function create(): void
    {
        $this->logger->debug('Create entity', ['table' => self::getTableName()]);
        try {
            $this->traitCreate();
        } catch (Throwable $exception) {
            $this->logger->error(
                'Failed to create entity',
                ['table' => self::getTableName(), 'exception' => $exception]
            );
            throw $exception;
        }
    }

    /**
     * @inheritDoc
     */
    public function delete(): void
    {
        $this->logger->debug(
            'Delete entity',
            ['table' => self::getTableName(), 'id' => $this->getId()]
        );
        try {
            $this->traitDelete();
        } catch (Throwable $exception) {
            $this->logger->error(
                'Failed to delete entity',
                [
                    'table' => self::getTableName(),
                    'id' => $this->getId(),
                    'exception' => $exception
                ]
            );
            throw $exception;
        }
    }

    /**
     * @inheritDoc
     */
    public static function findAll(string $orderBy = 'id ASC'): Iterator
    {
        $logger = Logger::getLogger();
        try {
            $logger->debug('Find all entities', ['table' => self::getTableName()]);
            return self::traitFindAll($orderBy);
        } catch (Throwable $exception) {
            $logger->error(
                'Failed to find all entity',
                [
                    'table' => self::getTableName(),
                    'exception' => $exception
                ]
            );
            throw $exception;
        }
    }

    /**
     * @inheritDoc
     */
    public static function findById(int|string $id): static|null
    {
        $logger = Logger::getLogger();
        try {
            $logger->debug('Find entity by id', ['table' => self::getTableName(), 'id' => $id]);
            return self::traitFindById($id);
        } catch (Throwable $exception) {
            $logger->error(
                'Failed to find entity by id',
                [
                    'table' => self::getTableName(),
                    'id' => $id,
                    'exception' => $exception
                ]
            );
            throw $exception;
        }
    }

    /**
     * @inheritDoc
     */
    public static function findRange(int $start, int $count, string $orderBy = 'id ASC'): Iterator
    {
        $logger = Logger::getLogger();
        try {
            $logger->debug(
                'Find entities in range',
                [
                    'table' => self::getTableName(),
                    'start' => $start,
                    'count' => $count,
                    'orderBy' => $orderBy
                ]
            );
            return self::traitFindRange($start, $count, $orderBy);
        } catch (Throwable $exception) {
            $logger->error(
                'Failed to find entities in range',
                [
                    'table' => self::getTableName(),
                    'start' => $start,
                    'count' => $count,
                    'orderBy' => $orderBy,
                    'exception' => $exception
                ]
            );
            throw $exception;
        }
    }

    /**
     * @inheritDoc
     * @param array<string, array<string, mixed>> $filters
     */
    public static function findByFilters(array $filters, string $orderBy = 'id ASC'): Iterator
    {
        $logger = Logger::getLogger();
        try {
            $logger->debug(
                'Find entities by filters',
                [
                    'table' => self::getTableName(),
                    'filters' => $filters,
                    'orderBy' => $orderBy
                ]
            );
            return self::traitFindByFilters($filters, $orderBy);
        } catch (Throwable $exception) {
            $logger->error(
                'Failed to find entities by filters',
                [
                    'table' => self::getTableName(),
                    'filters' => $filters,
                    'orderBy' => $orderBy,
                    'exception' => $exception
                ]
            );
            throw $exception;
        }
    }

    /**
     * @inheritDoc
     */
    public static function countAll(): int
    {
        $logger = Logger::getLogger();
        try {
            $logger->debug(
                'Count all entities',
                [
                    'table' => self::getTableName()
                ]
            );
            return self::traitCountAll();
        } catch (Throwable $exception) {
            $logger->error(
                'Failed to count all entities',
                [
                    'table' => self::getTableName(),
                    'exception' => $exception
                ]
            );
            throw $exception;
        }
    }

    /**
     * @inheritDoc
     */
    public static function countByFilters(array $filters): int
    {
        $logger = Logger::getLogger();
        try {
            $logger->debug(
                'Count entities by filters',
                [
                    'table' => self::getTableName(),
                    'filters' => $filters
                ]
            );
            return self::traitCountByFilters($filters);
        } catch (Throwable $exception) {
            $logger->error(
                'Failed to count entities by filters',
                [
                    'table' => self::getTableName(),
                    'filters' => $filters,
                    'exception' => $exception
                ]
            );
            throw $exception;
        }
    }

    /**
     * @inheritDoc
     */
    public function update(): void
    {
        $this->logger->debug(
            'Update entity',
            ['table' => self::getTableName(), 'id' => $this->getId()]
        );
        try {
            $this->traitUpdate();
        } catch (Throwable $exception) {
            $this->logger->error(
                'Failed to update entity',
                [
                    'table' => self::getTableName(),
                    'id' => $this->getId(),
                    'exception' => $exception
                ]
            );
            throw $exception;
        }
    }

    public function jsonSerialize(): mixed
    {
        return $this->format();
    }
}
