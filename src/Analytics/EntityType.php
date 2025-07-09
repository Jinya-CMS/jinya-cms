<?php

namespace Jinya\Cms\Analytics;

enum EntityType
{
    case BlogPost;
    case BlogCategory;
    case Form;
    case Gallery;
    case ModernPage;
    case ClassicPage;
    case Artist;

    public function int(): int
    {
        return (int)array_search($this, self::cases(), true);
    }

    public function string(): string
    {
        return match ($this) {
            self::BlogPost => 'blog-post',
            self::BlogCategory => 'blog-category',
            self::ModernPage => 'modern-page',
            self::ClassicPage => 'classic-page',
            default => lcfirst($this->name)
        };
    }

    public static function fromInt(int $value): self
    {
        if (array_key_exists($value, self::cases())) {
            return self::cases()[$value];
        }

        return self::BlogPost;
    }

    public static function fromString(string $entityType): EntityType
    {
        return match ($entityType) {
            'blog-post' => self::BlogPost,
            'blog-category' => self::BlogCategory,
            'form' => self::Form,
            'gallery' => self::Gallery,
            'modern-page' => self::ModernPage,
            'classic-page' => self::ClassicPage,
            'artist' => self::Artist,
            default => self::BlogPost,
        };
    }
}
