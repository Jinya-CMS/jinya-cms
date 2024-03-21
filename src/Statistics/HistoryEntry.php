<?php

namespace App\Statistics;

use App\Database\Utils\FormattableEntityInterface;
use JetBrains\PhpStorm\ArrayShape;
use JsonSerializable;

/**
 * A history entry contains the count and date of changes
 * @codeCoverageIgnore
 */
class HistoryEntry implements JsonSerializable, FormattableEntityInterface
{
    /** @var int The count of entities changed at the given date */
    public int $count;
    /** @var string The date the entities were changed */
    public string $date;

    /**
     * Serializes the data into JSON
     *
     * @return array<string, int|string>
     */
    #[ArrayShape(['count' => 'int', 'date' => 'string'])] public function jsonSerialize(): array
    {
        return [
            'count' => $this->count,
            'date' => $this->date,
        ];
    }

    /**
     * Formats the data into an array
     *
     * @return array<string, int|string>
     */
    #[ArrayShape(['count' => 'int', 'date' => 'string'])] public function format(): array
    {
        return [
            'count' => $this->count,
            'date' => $this->date,
        ];
    }
}
