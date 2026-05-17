<?php

namespace Jinya\Cms\Database;

use JetBrains\PhpStorm\ArrayShape;
use Jinya\Database\Attributes\Column;
use Jinya\Database\Attributes\Table;
use Jinya\Database\Creatable;
use Jinya\Database\Deletable;
use Jinya\Database\DeletableEntityTrait;
use Jinya\Database\EntityTrait;
use Jinya\Database\Updatable;

/**
 * This class contains a gallery connected to a theme
 */
#[Table('theme_gallery')]
class ThemeGallery implements Creatable, Updatable, Deletable
{
    use EntityTrait;
    use DeletableEntityTrait;
    use ThemeLinkTrait;

    /** @var string The theme name */
    #[Column]
    public string $name = '';

    /** @var int The theme id */
    #[Column(sqlName: 'theme_id')]
    public int $themeId = -1;

    /** @var int The gallery ID */
    #[Column(sqlName: 'gallery_id')]
    public int $galleryId = -1;

    private static function getLinkIdProperty(): string
    {
        return 'galleryId';
    }

    private static function getLinkIdColumn(): string
    {
        return 'gallery_id';
    }

    /**
     * Formats the theme gallery into an array
     *
     * @return array<string, array<string, array<string, array<string, string|null>|string>|int|string>|string|null>
     */
    #[ArrayShape(['name' => 'string', 'gallery' => 'array'])]
    public function format(): array
    {
        return [
            'name' => $this->name,
            'gallery' => $this->getGallery()?->format(),
        ];
    }

    /**
     * Gets the gallery of the theme gallery
     *
     * @return Gallery|null
     */
    public function getGallery(): ?Gallery
    {
        return Gallery::findById($this->galleryId);
    }
}
