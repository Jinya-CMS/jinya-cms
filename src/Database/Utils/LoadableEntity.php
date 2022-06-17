<?php

namespace App\Database\Utils;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use Iterator;
use JetBrains\PhpStorm\Pure;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;
use Jinya\PDOx\PDOx;
use Laminas\Hydrator\NamingStrategy\UnderscoreNamingStrategy;
use Laminas\Hydrator\ReflectionHydrator;
use Laminas\Hydrator\Strategy\StrategyInterface;
use PDO;
use PDOException;

/**
 * Base class for all entity classes. this class contains several helper methods and provides a common interface for all entities.
 */
abstract class LoadableEntity implements FormattableEntityInterface
{
    /** @var string The date format for MySQL, MariaDB, Percona etc. */
    public const MYSQL_DATE_FORMAT = 'Y-m-d H:i:s';
    /** @var PDOx|null The currently active PDOx instance. Needs to be accessed via getPdo function, since it will be instantiated if it is currently null */
    protected static ?PDOx $pdo;
    /** @var int|string The ID of the entity, usually all entities have an ID and that ID must be either a string or an int */
    public int|string $id = -1;

    /**
     * Needs to be implemented to find the derived class by id
     *
     * @param int $id The id of the entity
     * @return object|null
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    abstract public static function findById(int $id): ?object;

    /**
     * Gets all entities that match the given keyword
     *
     * @param string $keyword
     * @return Iterator
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    abstract public static function findByKeyword(string $keyword): Iterator;

    /**
     * Gets all entities of the given type
     *
     * @return Iterator
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    abstract public static function findAll(): Iterator;

    /**
     * Executes the given sql statement
     *
     * @param string $sql The sql statement to execute
     * @return array<mixed>|int
     */
    public static function executeSqlString(string $sql): array|int
    {
        $pdo = self::getPdo();
        $stmt = $pdo->query($sql);
        if ($stmt && $stmt->columnCount() > 0) {
            return $stmt->fetchAll();
        }

        return $stmt ? $stmt->rowCount() : 0;
    }

    /**
     * Gets the currently active instance of PDOx
     *
     * @return PDOx
     */
    public static function getPdo(): PDOx
    {
        if (isset(self::$pdo) && self::$pdo !== null) {
            return self::$pdo;
        }

        $database = getenv('MYSQL_DATABASE');
        $user = getenv('MYSQL_USER') ?: '';
        $password = getenv('MYSQL_PASSWORD') ?: '';
        $host = getenv('MYSQL_HOST') ?: '127.0.0.1';
        $port = getenv('MYSQL_PORT') ?: 3306;
        $charset = getenv('MYSQL_CHARSET') ?: 'utf8mb4';
        $pdo = new PDOx(
            "mysql:host=$host;port=$port;dbname=$database",
            $user,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
        $pdo->exec("set names $charset");
        self::$pdo = $pdo;

        return $pdo;
    }

    /**
     * Executes the given sql statement and returns an int, string, false or null
     *
     * @param string $sql The sql statement to execute
     * @return int|string|false|null
     */
    public static function fetchColumn(string $sql): int|string|false|null
    {
        $pdo = self::getPdo();
        $stmt = $pdo->query($sql);
        if ($stmt && $stmt->columnCount() > 0) {
            return $stmt->fetchColumn();
        }

        return $stmt ? $stmt->rowCount() : 0;
    }

    /**
     * Creates a new instance of ReflectionHydrator using the provided strategies
     *
     * @param array<StrategyInterface> $additionalStrategies The additional strategies to use with the ReflectionHydrator
     * @return ReflectionHydrator
     */
    protected static function getHydrator(array $additionalStrategies): ReflectionHydrator
    {
        $hydrator = new ReflectionHydrator();
        $hydrator->setNamingStrategy(new UnderscoreNamingStrategy());
        foreach ($additionalStrategies as $key => $additionalStrategy) {
            $hydrator->addStrategy($key, $additionalStrategy);
        }

        return $hydrator;
    }

    /**
     * Fetches a single entity by the given id
     *
     * @param string $table The table to fetch the entity from
     * @param int $id The ID the entity to fetch has
     * @param mixed $prototype An initialized instance of the class to hydrate the values into
     * @param StrategyInterface[] $additionalStrategies The additional strategies used for hydration
     * @return mixed
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     * @throws NoResultException
     * @throws NoResultException
     */
    protected static function fetchSingleById(
        string $table,
        int    $id,
        mixed  $prototype,
        array  $additionalStrategies = []
    ): mixed
    {
        $sql = "SELECT * FROM $table WHERE id = :id";

        try {
            return self::getPdo()->fetchObject($sql, $prototype, ['id' => $id], $additionalStrategies);
        } catch (InvalidQueryException $e) {
            throw self::convertInvalidQueryExceptionToException($e);
        }
    }

    /**
     * Converts the generic InvalidQueryException of PDOx and returns a more specialized exception or the original exception, if there is no more specific exception
     *
     * @param InvalidQueryException $exception The exception to convert
     * @return UniqueFailedException|ForeignKeyFailedException|InvalidQueryException
     */
    #[Pure]
    protected static function convertInvalidQueryExceptionToException(
        InvalidQueryException $exception
    ): UniqueFailedException|ForeignKeyFailedException|InvalidQueryException
    {
        if (!empty($exception->errorInfo[1])) {
            return match ($exception->errorInfo[1]) {
                1062 => new UniqueFailedException($exception->getMessage(), 1062, $exception, $exception->errorInfo),
                1452 => new ForeignKeyFailedException($exception->getMessage(), 1452, $exception, $exception->errorInfo),
                1451 => new ForeignKeyFailedException($exception->getMessage(), 1451, $exception, $exception->errorInfo),
                default => $exception,
            };
        }

        return $exception;
    }

    /**
     * Fetches all data in a table and returns an Iterator
     *
     * @param string $table The table to fetch the data from
     * @param mixed $prototype An initialized instance of the class to hydrate the values into
     * @param StrategyInterface[] $additionalStrategies
     * @return Iterator
     * @throws ForeignKeyFailedException
     * @throws UniqueFailedException
     * @throws InvalidQueryException
     */
    protected static function fetchAllIterator(string $table, mixed $prototype, array $additionalStrategies = []): Iterator
    {
        try {
            return self::getPdo()->fetchIterator("SELECT * FROM $table", $prototype, null, $additionalStrategies);
        } catch (InvalidQueryException $e) {
            throw self::convertInvalidQueryExceptionToException($e);
        }
    }

    /**
     * Gets the ID of the entity as integer value
     *
     * @return int
     */
    public function getIdAsInt(): int
    {
        return (int)$this->id;
    }

    /**
     * Creates the given entity
     *
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    abstract public function create(): void;

    /**
     * Deletes the given entity
     *
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    abstract public function delete(): void;

    /**
     * Updates the given entity
     *
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    abstract public function update(): void;

    /**
     * Function to create the current entity
     *
     * @param string $table The table to create the entity in
     * @param array<StrategyInterface> $strategies An array of additional hydration strategies
     * @param array<string> $skippedFields The fields to not include in the sql statement
     * @return int The new id
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    protected function internalCreate(string $table, array $strategies = [], array $skippedFields = []): int
    {
        $hydrator = self::getHydrator($strategies);
        $hydrator->addFilter('excludes', new SkipFieldFilter([...$skippedFields, 'id', 'pdo']));

        $extracted = $hydrator->extract($this);
        $keys = array_keys($extracted);
        $columns = implode(',', $keys);
        $values = implode(',', array_map(static fn(string $value) => ":$value", $keys));
        $sql = "INSERT INTO $table ($columns) VALUES ($values)";
        self::executeStatement($sql, $extracted);

        $this->id = (int)self::getPdo()->lastInsertId();

        return $this->id;
    }

    /**
     * Executes the given statement and returns the result
     *
     * @param string $statement The sql statement to execute
     * @param array<string, int|string|bool> $parameters The parameters for the parametrized query
     * @return array<mixed>|int Either a count of affected rows or an array of data
     * @throws ForeignKeyFailedException
     * @throws UniqueFailedException
     * @throws InvalidQueryException|InvalidQueryException
     */
    protected static function executeStatement(string $statement, array $parameters = []): array|int
    {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare($statement);
        try {
            if ($stmt === false) {
                throw new PDOException();
            }

            $stmt->execute($parameters);
            if ($stmt->errorCode() !== '00000') {
                $ex = new InvalidQueryException(errorInfo: $stmt->errorInfo());
                throw self::convertInvalidQueryExceptionToException($ex);
            }

            if ($stmt->columnCount() > 0) {
                return $stmt->fetchAll();
            }

            return $stmt->rowCount();
        } catch (PDOException $exception) {
            $ex = new InvalidQueryException(errorInfo: $exception->errorInfo);
            throw self::convertInvalidQueryExceptionToException($ex);
        }
    }

    /**
     * Function to delete the current entity
     *
     * @param string $table The table to delete the entity from
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    protected function internalDelete(string $table): void
    {
        $sql = "DELETE FROM $table WHERE id = :id";
        self::executeStatement($sql, ['id' => $this->id]);
    }

    /**
     * Function to update the current entity
     *
     * @param string $table The table to update the entity in
     * @param array<StrategyInterface> $strategies An array of additional hydration strategies
     * @param array<string> $skippedFields The fields to not include in the sql statement
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    protected function internalUpdate(string $table, array $strategies = [], array $skippedFields = []): void
    {
        $hydrator = self::getHydrator($strategies);
        $hydrator->addFilter('excludes', new SkipFieldFilter([...$skippedFields, 'id', 'pdo']));

        $extracted = $hydrator->extract($this);
        $update = [];
        foreach ($extracted as $key => $item) {
            $update[] = "$key=:$key";
        }
        $setInstructions = implode(',', $update);
        $sql = "UPDATE $table SET $setInstructions WHERE id = :id";
        $extracted['id'] = $this->id;
        self::executeStatement($sql, $extracted);
    }
}