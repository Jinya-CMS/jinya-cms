<?php

namespace Jinya\Components\Database;

use JsonSerializable;

class TableFieldMetadata implements JsonSerializable
{
    private string $field;

    private string $type;

    private bool $nullable;

    private string $key;

    private ?string $default;

    private string $extra;

    public function getField(): string
    {
        return $this->field;
    }

    public function setField(string $field): void
    {
        $this->field = $field;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function isNullable(): bool
    {
        return $this->nullable;
    }

    public function setNullable(bool $nullable): void
    {
        $this->nullable = $nullable;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function setKey(string $key): void
    {
        $this->key = $key;
    }

    public function getDefault(): ?string
    {
        return $this->default;
    }

    public function setDefault(?string $default): void
    {
        $this->default = $default;
    }

    public function getExtra(): string
    {
        return $this->extra;
    }

    public function setExtra(string $extra): void
    {
        $this->extra = $extra;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'field' => $this->field,
            'extra' => $this->extra,
            'default' => $this->default,
            'key' => $this->key,
            'type' => $this->type,
            'nullable' => $this->nullable,
        ];
    }
}
