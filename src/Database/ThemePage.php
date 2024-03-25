<?php

namespace App\Database;

use Iterator;
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
class ThemePage implements Creatable, Updatable, Deletable
{
    use EntityTrait;
    use DeletableEntityTrait;

    /** @var string The theme name */
    #[Column]
    public string $name = '';

    /** @var int The theme id */
    #[Column(sqlName: 'theme_id')]
    public int $themeId = -1;

    /** @var int The simple page ID */
    #[Column(sqlName: 'page_id')]
    public int $pageId = -1;

    /**
     * Finds a page by name and theme
     *
     * @param int $themeId
     * @param string $name
     * @return ThemePage|null
     */
    public static function findByThemeAndName(int $themeId, string $name): ?ThemePage
    {
        $query = self::getQueryBuilder()
            ->newSelect()
            ->from(self::getTableName())
            ->cols([
                'theme_id',
                'name',
                'page_id',
            ])
            ->where('theme_id = :themeId AND name = :name', ['themeId' => $themeId, 'name' => $name]);

        $data = self::executeQuery($query);
        if (empty($data)) {
            return null;
        }

        return self::fromArray($data[0]);
    }

    /**
     * Finds the pages for the given theme
     *
     * @param int $themeId
     * @return Iterator<ThemePage>
     */
    public static function findByTheme(int $themeId): Iterator
    {
        $query = self::getQueryBuilder()
            ->newSelect()
            ->from(self::getTableName())
            ->cols([
                'theme_id',
                'name',
                'page_id',
            ])
            ->where('theme_id = :themeId', ['themeId' => $themeId]);

        $data = self::executeQuery($query);
        foreach ($data as $item) {
            yield self::fromArray($item);
        }
    }

    /**
     * Formats the theme page into an array
     *
     * @return array<string, array<string, array<string, array<string, string|null>|string>|int|string>|string|null>
     */
    #[ArrayShape(['name' => 'string', 'page' => 'array'])] public function format(): array
    {
        return [
            'name' => $this->name,
            'page' => $this->getPage()?->format(),
        ];
    }

    /**
     * Gets the page of the theme page
     *
     * @return SimplePage|null
     */
    public function getPage(): ?SimplePage
    {
        return SimplePage::findById($this->pageId);
    }

    /**
     * Creates the current theme page
     *
     * @return void
     */
    public function create(): void
    {
        $query = self::getQueryBuilder()
            ->newInsert()
            ->into(self::getTableName())
            ->addRow([
                'theme_id' => $this->themeId,
                'name' => $this->name,
                'page_id' => $this->pageId,
            ]);

        self::executeQuery($query);
    }

    /**
     * @inheritDoc
     */
    public function update(): void
    {
        $query = self::getQueryBuilder()
            ->newUpdate()
            ->table(self::getTableName())
            ->set('page_id', $this->pageId)
            ->where('theme_id = :themeId AND name = :name', ['themeId' => $this->themeId, 'name' => $this->name]);

        self::executeQuery($query);
    }
}
