<?php

namespace App\Maintenance\PhpInfo;

use JsonSerializable;

class IniValue implements JsonSerializable
{
    /** @var string|null */
    private ?string $value;

    /** @var string|null */
    private ?string $configName;

    /**
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @param string|null $value
     */
    public function setValue(?string $value): void
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getConfigName(): string
    {
        return $this->configName;
    }

    /**
     * @param string $configName
     */
    public function setConfigName(string $configName): void
    {
        $this->configName = $configName;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'value' => $this->value,
            'name' => $this->configName,
        ];
    }
}
