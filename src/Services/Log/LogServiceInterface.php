<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 31.10.2017
 * Time: 17:27
 */

namespace Jinya\Services\Log;

use Jinya\Entity\Logging\LogEntry;

interface LogServiceInterface
{
    /**
     * Finds all log messages that match the given filter
     * @param string $sortBy
     * @param string $sortOrder
     * @param string $level
     * @param string $filter
     */
    public function getAll(
        int $offset = 0,
        int $count = 20,
        $sortBy = 'createdAt',
        $sortOrder = 'desc',
        $level = 'info',
        $filter = ''
    ): array;

    /**
     * Finds the log message for the given id
     */
    public function get(int $id): LogEntry;

    /**
     * Counts all elements
     */
    public function countAll(): int;

    /**
     * Counts all elements based on the filters
     */
    public function countFiltered(string $level, string $filter): int;

    /**
     * Gets a list of all used log levels
     */
    public function getUsedLevels(): array;

    /**
     * Removes all log entries from the database and deletes the log files
     */
    public function clear(): void;
}
