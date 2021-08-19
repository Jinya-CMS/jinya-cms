<?php

namespace App\Maintenance\PhpInfo;

use JetBrains\PhpStorm\ArrayShape;
use JsonSerializable;

class PhpExtension implements JsonSerializable
{
    public string $extensionName;

    public array $iniValues = [];

    public string $version;

    public array $additionalData = [];

    /**
     * {@inheritdoc}
     */
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
