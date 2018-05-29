<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 31.10.2017
 * Time: 17:38
 */

namespace Jinya\Services\Log;

use Jinya\Entity\AccessLogEntry;

interface AccessLogServiceInterface
{
    /**
     * Finds all access log messages in the given range
     * @param int $offset
     * @param int $count
     * @param string $sortBy
     * @param string $sortOrder
     * @return array
     */
    public function getAll(int $offset = 0, int $count = 20, string $sortBy = 'createdAt', string $sortOrder = 'DESC'): array;

    /**
     * Finds the access log message for the given id
     * @param int $id
     * @return AccessLogEntry
     */
    public function get(int $id): AccessLogEntry;

    /**
     * Counts all access log entries in the database
     * @return int
     */
    public function countAll(): int;

    /**
     * Clears the access log
     */
    public function clear(): void;
}
