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
 * This class contains a simple page connected to a theme
 */
#[Table('theme_page')]
class ThemeClassicPage implements Creatable, Updatable, Deletable
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

    /** @var int The simple page ID */
    #[Column(sqlName: 'page_id')]
    public int $classicPageId = -1;

    private static function getLinkIdProperty(): string
    {
        return 'classicPageId';
    }

    private static function getLinkIdColumn(): string
    {
        return 'page_id';
    }

    /**
     * Formats the theme page into an array
     *
     * @return array<string, array<string, array<string, array<string, string|null>|string>|int|string>|string|null>
     */
    #[ArrayShape(['name' => 'string', 'classicPage' => 'array'])]
    public function format(): array
    {
        return [
            'name' => $this->name,
            'classicPage' => $this->getClassicPage()?->format(),
        ];
    }

    /**
     * Gets the page of the theme page
     *
     * @return ClassicPage|null
     */
    public function getClassicPage(): ?ClassicPage
    {
        return ClassicPage::findById($this->classicPageId);
    }
}
