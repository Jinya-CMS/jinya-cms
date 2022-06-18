<?php

namespace App\Maintenance\PhpInfo;

use JetBrains\PhpStorm\ArrayShape;
use JsonSerializable;
use ReturnTypeWillChange;

/**
 * This class contains a PHP INI value
 */
class IniValue implements JsonSerializable
{
    /** @var string|null The value of the INI value */
    public ?string $value;

    /** @var string|null The name of the INI value */
    public ?string $configName;

    /**
     * Serializes the INI value data into an array
     *
     * @return array<string, string|null>
     */
    #[ReturnTypeWillChange]
    #[ArrayShape(['value' => 'null|string', 'name' => 'null|string'])] public function jsonSerialize()
    {
        return [
            'value' => $this->value,
            'name' => $this->configName,
        ];
    }
}
