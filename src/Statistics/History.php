<?php

namespace App\Statistics;

use App\Database\Utils\LoadableEntity;
use Iterator;
use Jinya\PDOx\Exceptions\InvalidQueryException;

/**
 * Helper class to get the history of the given entity type
 * @codeCoverageIgnore
 */
abstract class History
{
    /**
     * Gets the create history of the given entity type
     *
     * @param string $type The entity type to check for
     * @return Iterator<HistoryEntry>
     * @throws InvalidQueryException
     */
    public static function getCreatedHistory(string $type): Iterator
    {
        return LoadableEntity::getPdo()->fetchIterator("SELECT COUNT(created_at) AS `count`, date(`created_at`) AS date FROM $type GROUP BY date(`created_at`)", new HistoryEntry());
    }

    /**
     * Gets the update history of the given entity type
     *
     * @param string $type The entity type to check for
     * @return Iterator<HistoryEntry>
     * @throws InvalidQueryException
     */
    public static function getUpdatedHistory(string $type): Iterator
    {
        return LoadableEntity::getPdo()->fetchIterator("SELECT COUNT(last_updated_at) AS count, date(`last_updated_at`) AS date FROM $type GROUP BY date(`last_updated_at`)", new HistoryEntry());
    }
}