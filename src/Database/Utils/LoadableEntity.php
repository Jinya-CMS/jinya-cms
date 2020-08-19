<?php

namespace App\Database\Utils;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Message;
use Exception;
use Iterator;
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\Adapter\Driver\StatementInterface;
use Laminas\Db\Adapter\Exception\InvalidQueryException;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Db\Sql\Sql;
use Laminas\Hydrator\NamingStrategy\UnderscoreNamingStrategy;
use Laminas\Hydrator\ReflectionHydrator;
use Laminas\Hydrator\Strategy\StrategyInterface;
use PDOException;

abstract class LoadableEntity
{
    public const MYSQL_DATE_FORMAT = 'Y-m-d H:i:s';
    private static Adapter $adapter;
    public $id = -1;

    /**
     * @param int $id
     * @return mixed
     */
    abstract public static function findById(int $id);

    /**
     * Gets all entities that match the given keyword
     *
     * @param string $keyword
     * @return Iterator
     */
    abstract public static function findByKeyword(string $keyword): Iterator;

    /**
     * Gets all entities of the given type
     *
     * @return Iterator
     */
    abstract public static function findAll(): Iterator;

    /**
     * Executes the given sql statement
     *
     * @param string $sql
     * @return array|int
     */
    public static function executeSqlString(string $sql)
    {
        $result = self::getAdapter()->getDriver()->getConnection()->execute($sql);
        if ($result->isQueryResult()) {
            return iterator_to_array($result);
        }

        return $result->getAffectedRows();
    }

    /**
     * Gets an adapter for the current database
     *
     * @return Adapter
     */
    protected static function getAdapter(): Adapter
    {
        if (!isset(self::$adapter)) {
            self::$adapter = new Adapter([
                'driver' => 'Pdo_Mysql',
                'database' => getenv('MYSQL_DATABASE'),
                'username' => getenv('MYSQL_USER'),
                'password' => getenv('MYSQL_PASSWORD'),
                'hostname' => getenv('MYSQL_HOST') ?: '127.0.0.1',
                'port' => getenv('MYSQL_PORT') ?: 3306,
                'charset' => getenv('MYSQL_CHARSET') ?: 'utf8mb4',
            ]);
        }

        return self::$adapter;
    }

    /**
     * Fetches a single entity by the given id
     *
     * @param string $table
     * @param int $id
     * @param StrategyInterface[] $additionalStrategies
     * @return Message
     * @noinspection PhpIncompatibleReturnTypeInspection
     */
    protected static function fetchSingleById(string $table, int $id, $prototype, array $additionalStrategies = [])
    {
        $sql = self::getSql();
        $select = $sql->select()->from($table)->where(['id = :id']);

        $result = self::executeStatement($sql->prepareStatementForSqlObject($select), ['id' => $id]);

        return self::hydrateSingleResult($result, $prototype, $additionalStrategies);
    }

    /**
     * @return Sql
     */
    protected static function getSql(): Sql
    {
        return new Sql(self::getAdapter());
    }

    /**
     * Executes the given statement and returns the result
     *
     * @param StatementInterface $statement
     * @param array $parameters
     * @return ResultInterface
     */
    protected static function executeStatement(StatementInterface $statement, array $parameters = []): ResultInterface
    {
        $statement->prepare();

        return $statement->execute($parameters);
    }

    /**
     * Hydrates the result using the given prototype and returns the object that was hydrated
     *
     * @param ResultInterface $result
     * @param $prototype
     * @param StrategyInterface[] $additionalStrategies
     * @return object
     */
    protected static function hydrateSingleResult(ResultInterface $result, $prototype, array $additionalStrategies = [])
    {
        $resultSet = self::getHydrator($additionalStrategies, $prototype, $result);

        $resultSet->rewind();
        if (!$resultSet->valid()) {
            return null;
        }

        return $resultSet->current();
    }

    /**
     * @param array $additionalStrategies
     * @param $prototype
     * @param ResultInterface $result
     * @return HydratingResultSet
     */
    protected static function getHydrator(
        array $additionalStrategies,
        $prototype,
        ResultInterface $result
    ): HydratingResultSet {
        $hydrator = new ReflectionHydrator();
        $hydrator->setNamingStrategy(new UnderscoreNamingStrategy());
        foreach ($additionalStrategies as $key => $additionalStrategy) {
            $hydrator->addStrategy($key, $additionalStrategy);
        }

        $resultSet = new HydratingResultSet($hydrator, $prototype);
        $resultSet->initialize($result);

        return $resultSet;
    }

    /**
     * Fetches all data in a table and creates a array
     *
     * @param string $table
     * @param $prototype
     * @param StrategyInterface[] $additionalStrategies
     * @return Iterator
     */
    protected static function fetchArray(
        string $table,
        $prototype,
        array $additionalStrategies = []
    ): Iterator {
        $sql = new Sql(self::getAdapter());
        $select = $sql->select($table);

        $result = self::executeStatement($sql->prepareStatementForSqlObject($select));

        return self::hydrateMultipleResults($result, $prototype, $additionalStrategies);
    }

    /**
     * Hydrates the result using the given prototype as array
     *
     * @param ResultInterface $result
     * @param mixed $prototype
     * @param StrategyInterface[] $additionalStrategies
     * @return Iterator
     */
    protected static function hydrateMultipleResults(
        ResultInterface $result,
        $prototype,
        array $additionalStrategies = []
    ): Iterator {
        return self::getHydrator($additionalStrategies, $prototype, $result);
    }

    public function getIdAsInt(): int
    {
        return (int)$this->id;
    }

    public function getIdAsString(): string
    {
        return (string)$this->id;
    }

    /**
     * Creates the given entity
     *
     * @throws UniqueFailedException
     */
    abstract public function create(): void;

    /**
     * Deletes the given entity
     */
    abstract public function delete(): void;

    /**
     * Updates the given entity
     *
     * @throws UniqueFailedException
     */
    abstract public function update(): void;

    /**
     * @param string $table
     * @param array $strategies
     * @param array $skippedFields
     * @return int
     * @throws Exception
     */
    protected function internalCreate(string $table, array $strategies = [], array $skippedFields = []): int
    {
        $hydrator = new ReflectionHydrator();
        $hydrator->setNamingStrategy(new UnderscoreNamingStrategy());
        foreach ($strategies as $key => $strategy) {
            $hydrator->addStrategy($key, $strategy);
        }
        $hydrator->addFilter('excludes', new SkipFieldFilter([...$skippedFields, 'id']));

        $sql = self::getSql();
        $insert = $sql->insert($table);
        $insert->values($hydrator->extract($this));
        try {
            self::executeStatement($sql->prepareStatementForSqlObject($insert));
        } catch (InvalidQueryException $exception) {
            throw $this->convertInvalidQueryExceptionToException($exception);
        }

        $result = $sql->getAdapter()->driver->getConnection()->execute('SELECT LAST_INSERT_ID() as id');
        $this->id = (int)$result->current()['id'];

        return $this->id;
    }

    /**
     * @param Exception $exception
     * @return Exception
     */
    protected function convertInvalidQueryExceptionToException(Exception $exception): Exception
    {
        /** @var PDOException $previous */
        $previous = $exception->getPrevious();
        switch ($previous->errorInfo[1]) {
            case 1062:
                return new UniqueFailedException($exception);
            case 1452:
                return new ForeignKeyFailedException($exception);
        }

        return $exception;
    }

    /**
     * @param string $table
     */
    protected function internalDelete(string $table): void
    {
        $sql = self::getSql();
        $delete = $sql->delete()->from($table)->where(['id = :id']);
        self::executeStatement($sql->prepareStatementForSqlObject($delete), ['id' => $this->id]);
    }

    /**
     * @param string $table
     * @param array $strategies
     * @throws UniqueFailedException
     * @throws Exception
     */
    protected function internalUpdate(string $table, array $strategies = []): void
    {
        $sql = self::getSql();
        $hydrator = new ReflectionHydrator();
        $hydrator->setNamingStrategy(new UnderscoreNamingStrategy());
        foreach ($strategies as $key => $strategy) {
            $hydrator->addStrategy($key, $strategy);
        }

        $data = $hydrator->extract($this);
        $params = [];
        foreach ($data as $key => $value) {
            if ($key === 'id') {
                continue;
            }
            $params[$key] = $value;
        }

        $update = $sql->update($table)
            ->where(['id = :id'])
            ->set($params);

        try {
            self::executeStatement($sql->prepareStatementForSqlObject($update), ['id' => $this->id]);
        } catch (InvalidQueryException $exception) {
            throw $this->convertInvalidQueryExceptionToException($exception);
        }
    }
}