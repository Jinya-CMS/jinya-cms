<?php

namespace Jinya\Components\PhpInfo;

use JsonSerializable;

class IniValue implements JsonSerializable
{
    /** @var string */
    private ?string $value;

    /** @var string */
    private ?string $configName;

    /**
     * @return string
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue(?string $value): void
    {
        $this->value = $value;
    }

    public function getConfigName(): string
    {
        return $this->configName;
    }

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
