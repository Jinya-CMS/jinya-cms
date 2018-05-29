<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 26.02.2018
 * Time: 08:07
 */

namespace Jinya\Services\History;

interface HistoryServiceInterface
{
    /**
     * Gets the history for the specified entity
     *
     * @param string $class
     * @param int $id
     * @return array
     */
    public function getHistory(string $class, int $id): array;

    /**
     * Clears the history for the specified entity
     *
     * @param string $class
     * @param int $id
     */
    public function clearHistory(string $class, int $id): void;

    /**
     * Revert the given field to the specified value
     *
     * @param string $class
     * @param int $id
     * @param string $field
     * @param string $timestamp
     */
    public function revert(string $class, int $id, string $field, string $timestamp): void;
}
