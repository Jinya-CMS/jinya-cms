<?php

namespace App\Statistics;

use App\Database\Utils\LoadableEntity;
use Iterator;
use Jinya\PDOx\Exceptions\InvalidQueryException;

/**
 *
 */
class History
{
    /**
     * @param string $type
     * @return Iterator<HistoryEntry>
     * @throws InvalidQueryException
     */
    public function getCreatedHistory(string $type): Iterator
    {
        return LoadableEntity::getPdo()->fetchIterator("SELECT COUNT(created_at) AS `count`, date(`created_at`) AS date FROM $type GROUP BY date(`created_at`)", new HistoryEntry());
    }

    /**
     * @param string $type
     * @return Iterator<HistoryEntry>
     * @throws InvalidQueryException
     */
    public function getUpdatedHistory(string $type): Iterator
    {
        return LoadableEntity::getPdo()->fetchIterator("SELECT COUNT(last_updated_at) AS count, date(`last_updated_at`) AS date FROM $type GROUP BY date(`last_updated_at`)", new HistoryEntry());
    }
}