<?php

namespace App\Statistics;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Helper class to get storage statistics
 */
abstract class Storage
{
    /**
     * Gets the total available storage on the system
     *
     * @return float
     */
    public static function getTotalStorage(): float
    {
        return disk_total_space(__ROOT__) ?: 0;
    }

    /**
     * Gets the free available storage on the system
     *
     * @return float
     */
    public static function getFreeStorage(): float
    {
        return disk_free_space(__ROOT__) ?: 0;
    }

    /**
     * Gets the storage used by Jinya CMS
     *
     * @return float
     */
    public static function getUsedStorage(): float
    {
        $size = 0;
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__ROOT__)) as $file) {
            $size += $file->getSize();
        }
        return $size;
    }
}