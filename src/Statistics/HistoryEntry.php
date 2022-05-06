<?php

namespace App\Statistics;

use App\Database\Utils\FormattableEntityInterface;
use JetBrains\PhpStorm\ArrayShape;
use JsonSerializable;

/**
 *
 */
class HistoryEntry implements JsonSerializable, FormattableEntityInterface
{
    public int $count;
    public string $date;

    #[ArrayShape(['count' => 'int', 'date' => 'string'])] public function jsonSerialize(): array
    {
        return [
            'count' => $this->count,
            'date' => $this->date,
        ];
    }

    #[ArrayShape(['count' => 'int', 'date' => 'string'])] public function format(): array
    {
        return [
            'count' => $this->count,
            'date' => $this->date,
        ];
    }
}