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
 * This class contains a segment page connected to a theme
 */
#[Table('theme_segment_page')]
class ThemeSegmentPage implements Creatable, Updatable, Deletable
{
    use EntityTrait;
    use DeletableEntityTrait;

    /** @var string The theme name */
    #[Column]
    public string $name = '';

    /** @var int The theme id */
    #[Column(sqlName: 'theme_id')]
    public int $themeId = -1;

    /** @var int The segment page ID */
    #[Column(sqlName: 'segment_page_id')]
    public int $segmentPageId = -1;

    /**
     * Finds a page by name and theme
     *
     * @param int $themeId
     * @param string $name
     * @return ThemeSegmentPage|null
     */
    public static function findByThemeAndName(int $themeId, string $name): ?ThemeSegmentPage
    {
        $query = self::getQueryBuilder()
            ->newSelect()
            ->from(self::getTableName())
            ->cols([
                'theme_id',
                'name',
                'segment_page_id',
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
     * @return Iterator<ThemeSegmentPage>
     */
    public static function findByTheme(int $themeId): Iterator
    {
        $query = self::getQueryBuilder()
            ->newSelect()
            ->from(self::getTableName())
            ->cols([
                'theme_id',
                'name',
                'segment_page_id',
            ])
            ->where('theme_id = :themeId', ['themeId' => $themeId]);

        $data = self::executeQuery($query);
        foreach ($data as $item) {
            yield self::fromArray($item);
        }
    }

    /**
     * Formats the theme segment page into an array
     *
     * @return array<string, array<string, array<string, array<string, string|null>|string>|int|string>|string|null>
     */
    #[ArrayShape(['name' => 'string', 'segmentPage' => 'array'])] public function format(): array
    {
        return [
            'name' => $this->name,
            'segmentPage' => $this->getSegmentPage()?->format(),
        ];
    }

    /**
     * Gets the page of the theme page
     *
     * @return SegmentPage|null
     */
    public function getSegmentPage(): ?SegmentPage
    {
        return SegmentPage::findById($this->segmentPageId);
    }

    /**
     * Creates the current theme segment page
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
                'segment_page_id' => $this->segmentPageId,
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
            ->set('segment_page_id', $this->segmentPageId)
            ->where('theme_id = :themeId AND name = :name', ['themeId' => $this->themeId, 'name' => $this->name]);

        self::executeQuery($query);
    }
}
