<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 31.10.2017
 * Time: 17:38
 */

namespace HelperBundle\Services\Log;


use HelperBundle\Entity\AccessLogEntry;

interface AccessLogServiceInterface
{
    /**
     * Finds all access log messages in the given range
     * @param int $offset
     * @param int $count
     * @return AccessLogEntry[]
     */
    public function getAll(int $offset = 0, int $count = 20): array;

    /**
     * Finds the access log message for the given id
     * @param int $id
     * @return AccessLogEntry
     */
    public function get(int $id): AccessLogEntry;
}