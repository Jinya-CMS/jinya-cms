<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 31.10.2017
 * Time: 17:27
 */

namespace HelperBundle\Services\Log;


use HelperBundle\Entity\LogEntry;

interface LogServiceInterface
{
    /**
     * Finds all log messages that match the given filter
     * @param int $offset
     * @param int $count
     * @param string $sortBy
     * @param string $sortOrder
     * @param string $filter
     * @return LogEntry[]
     */
    public function getAll(int $offset = 0, int $count = 20, $sortBy = 'createdAt', $sortOrder = 'desc', $filter = ''): array;

    /**
     * Finds the log message for the given id
     * @param int $id
     * @return LogEntry
     */
    public function get(int $id): LogEntry;
}