<?php

namespace Jinya\Cms\Analytics;

enum DeviceType
{
    case Smartphone;
    case Computer;
    case Tablet;
    case Other;

    public function int(): int
    {
        return (int)array_search($this, self::cases(), true);
    }

    public function string(): string
    {
        return strtolower($this->name);
    }

    public static function fromInt(int $value): self
    {
        if (array_key_exists($value, self::cases())) {
            return self::cases()[$value];
        }

        return self::Other;
    }
}
