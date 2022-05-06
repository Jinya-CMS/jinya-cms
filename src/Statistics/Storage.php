<?php

namespace App\Statistics;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 *
 */
class Storage
{
    public function getTotalStorage(): float
    {
        return disk_total_space(__ROOT__);
    }

    public function getFreeStorage(): float
    {
        return disk_free_space(__ROOT__);
    }

    public function getUsedStorage(): float
    {
        $size = 0;
        foreach (
            new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator(__ROOT__)
            ) as $file
        ) {
            $size += $file->getSize();
        }
        return $size;
    }
}