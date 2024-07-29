<?php

namespace Jinya\Cms\Database;

enum TotpMode
{
    case Email;
    case Otphp;

    public static function fromInt(int $value): self
    {
        if ($value === 1) {
            return TotpMode::Otphp;
        }

        return TotpMode::Email;
    }

    public static function fromString(string $value): self
    {
        if ($value === 'app') {
            return TotpMode::Otphp;
        }

        return TotpMode::Email;
    }

    public function int(): int
    {
        return match ($this) {
            self::Email => 0,
            self::Otphp => 1,
        };
    }

    public function string(): string
    {
        return match ($this) {
            self::Email => 'email',
            self::Otphp => 'app',
        };
    }
}
