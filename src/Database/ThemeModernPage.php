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
 * This class contains a segment page connected to a theme
 */
#[Table('theme_segment_page')]
class ThemeModernPage implements Creatable, Updatable, Deletable
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

    /** @var int The segment page ID */
    #[Column(sqlName: 'segment_page_id')]
    public int $modernPageId = -1;

    private static function getLinkIdProperty(): string
    {
        return 'modernPageId';
    }

    private static function getLinkIdColumn(): string
    {
        return 'segment_page_id';
    }

    /**
     * Formats the theme segment page into an array
     *
     * @return array<string, array<string, array<string, array<string, string|null>|string>|int|string>|string|null>
     */
    #[ArrayShape(['name' => 'string', 'modernPage' => 'array'])]
    public function format(): array
    {
        return [
            'name' => $this->name,
            'modernPage' => $this->getModernPage()?->format(),
        ];
    }

    /**
     * Gets the page of the theme page
     *
     * @return ModernPage|null
     */
    public function getModernPage(): ?ModernPage
    {
        return ModernPage::findById($this->modernPageId);
    }
}
