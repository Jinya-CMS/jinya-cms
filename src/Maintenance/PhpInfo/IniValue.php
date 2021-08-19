<?php

namespace App\Maintenance\PhpInfo;

use JetBrains\PhpStorm\ArrayShape;
use JsonSerializable;

class IniValue implements JsonSerializable
{
    public ?string $value;

    public ?string $configName;

    /**
     * {@inheritdoc}
     */
    #[ArrayShape(['value' => "null|string", 'name' => "null|string"])] public function jsonSerialize()
    {
        return [
            'value' => $this->value,
            'name' => $this->configName,
        ];
    }
}
