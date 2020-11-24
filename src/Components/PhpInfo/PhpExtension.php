<?php

namespace Jinya\Components\PhpInfo;

use JsonSerializable;

class PhpExtension implements JsonSerializable
{
    private string $extensionName;

    /** @var IniValue[] */
    private array $iniValues = [];

    private string $version;

    public function getExtensionName(): string
    {
        return $this->extensionName;
    }

    public function setExtensionName(string $extensionName): void
    {
        $this->extensionName = $extensionName;
    }

    /**
     * @return IniValue[]
     */
    public function getIniValues(): array
    {
        return $this->iniValues;
    }

    /**
     * @param IniValue[] $iniValues
     */
    public function setIniValues(array $iniValues): void
    {
        $this->iniValues = $iniValues;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function setVersion(string $version): void
    {
        $this->version = $version;
    }

    public function addIniValue(IniValue $iniValue): void
    {
        $this->iniValues[] = $iniValue;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'iniValues' => $this->iniValues,
            'version' => $this->version,
            'name' => $this->extensionName,
        ];
    }
}
