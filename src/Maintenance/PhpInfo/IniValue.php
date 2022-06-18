<?php

namespace App\Maintenance\PhpInfo;

use JetBrains\PhpStorm\ArrayShape;
use JsonSerializable;
use ReturnTypeWillChange;

/**
 *
 */
class IniValue implements JsonSerializable
{
    public ?string $value;

    public ?string $configName;

    /**
     * @inheritDoc
     */
    #[ReturnTypeWillChange] #[ArrayShape(['value' => 'null|string', 'name' => 'null|string'])] public function jsonSerialize()
    {
        return [
            'value' => $this->value,
            'name' => $this->configName,
        ];
    }
}
