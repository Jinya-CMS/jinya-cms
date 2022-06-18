<?php

namespace App\Maintenance\PhpInfo;

use JetBrains\PhpStorm\ArrayShape;
use JsonSerializable;
use ReturnTypeWillChange;
use stdClass;

/**
 *
 */
class PhpExtension implements JsonSerializable
{
    public string $extensionName;

    /** @var array<IniValue> */
    public array $iniValues = [];

    public string $version;

    /** @var array<string>|stdClass */
    public array|stdClass $additionalData = [];

    /**
     * @inheritDoc
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
