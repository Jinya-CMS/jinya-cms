<?php

namespace App\Maintenance\PhpInfo;

use JetBrains\PhpStorm\ArrayShape;
use JsonSerializable;
use ReturnTypeWillChange;
use stdClass;

/**
 * This class contains information about a PHP extension
 */
class PhpExtension implements JsonSerializable
{
    /** @var string The name of the extension */
    public string $extensionName;

    /** @var array<IniValue> The ini values of the extension */
    public array $iniValues = [];

    /** @var string The version of the extension, it usually equals the */
    public string $version;

    /** @var array<string>|stdClass The additional data available in an extension */
    public array|stdClass $additionalData = [];

    /**
     * Serializes the PHP extension data into an array
     *
     * @return array<string, mixed>
     */
    #[ReturnTypeWillChange]
    #[ArrayShape([
        'iniValues' => '\App\Maintenance\PhpInfo\IniValue[]',
        'version' => 'string',
        'name' => 'string',
        'additionalData' => 'array'
    ])] public function jsonSerialize()
    {
        return [
            'iniValues' => $this->iniValues,
            'version' => $this->version,
            'name' => $this->extensionName,
            'additionalData' => $this->additionalData,
        ];
    }
}
