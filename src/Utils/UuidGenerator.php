<?php

namespace App\Utils;

use Exception;

/**
 * Helper class to generate UUIDs
 */
abstract class UuidGenerator
{
    /**
     * Generates a new UUID of version 4
     *
     * @return string
     * @throws Exception
     */
    public static function generateV4(): string
    {
        $data = random_bytes(16);
        assert(strlen($data) === 16);

        // Set version to 0100
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        // Set bits 6-7 to 10
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        // Output the 36-character UUID.
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
