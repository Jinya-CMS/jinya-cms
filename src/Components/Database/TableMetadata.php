<?php

namespace Jinya\Components\Database;

use JsonSerializable;

class TableMetadata implements JsonSerializable
{
    /**
     * @var TableFieldMetadata[]
     */
    private array $fields;

    private string $checksum;

    /**
     * @return TableFieldMetadata[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @param TableFieldMetadata[] $fields
     */
    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }

    /**
     * @return string
     */
    public function getChecksum(): string
    {
        return $this->checksum;
    }

    /**
     * @param string $checksum
     */
    public function setChecksum(string $checksum): void
    {
        $this->checksum = $checksum;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [
            'checksum' => $this->checksum,
            'fields' => $this->fields,
        ];
    }
}