<?php

namespace App\Maintenance\PhpInfo;

use JsonSerializable;

class PhpExtension implements JsonSerializable
{
    /** @var string */
    private string $extensionName;

    /** @var IniValue[] */
    private array $iniValues = [];

    /** @var string */
    private string $version;

    /**
     * @return string
     */
    public function getExtensionName(): string
    {
        return $this->extensionName;
    }

    /**
     * @param string $extensionName
     */
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

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @param string $version
     */
    public function setVersion(string $version): void
    {
        $this->version = $version;
    }

    /**
     * @param IniValue $iniValue
     */
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
