<?php

namespace Jinya\Cms\Database;

use Iterator;
use JetBrains\PhpStorm\ArrayShape;
use Jinya\Database\Attributes\Column;
use Jinya\Database\Attributes\Table;
use Jinya\Database\Creatable;
use Jinya\Database\Deletable;
use Jinya\Database\DeletableEntityTrait;
use Jinya\Database\EntityTrait;
use Jinya\Database\Exception\ForeignKeyFailedException;
use Jinya\Database\Exception\UniqueFailedException;
use Jinya\Database\Updatable;
use PDOException;

/**
 * This class contains a simple page connected to a theme
 */
#[Table('theme_page')]
class ThemeClassicPage implements Creatable, Updatable, Deletable
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
    public int $classicPageId = -1;

    /**
     * Finds a page by name and theme
     *
     * @param int $themeId
     * @param string $name
     * @return ThemeClassicPage|null
     */
    public static function findByThemeAndName(int $themeId, string $name): ?ThemeClassicPage
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

        /** @var array<string, mixed>[] $data */
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
     * @return Iterator<ThemeClassicPage>
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

        /** @var array<string, mixed>[] $data */
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
    #[ArrayShape(['name' => 'string', 'classicPage' => 'array'])] public function format(): array
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
                'page_id' => $this->classicPageId,
            ]);

        try {
            self::executeQuery($query);
        } catch (PDOException $exception) {
            $errorInfo = $exception->errorInfo ?? ['', ''];
            if ($errorInfo[1] === 1062) {
                throw new UniqueFailedException($exception, self::getPDO());
            }

            if ($errorInfo[1] === 1452) {
                throw new ForeignKeyFailedException($exception, self::getPDO());
            }

            throw $exception;
        }
    }

    /**
     * @inheritDoc
     */
    public function update(): void
    {
        $query = self::getQueryBuilder()
            ->newUpdate()
            ->table(self::getTableName())
            /** @phpstan-ignore-next-line */
            ->set('page_id', $this->classicPageId)
            ->where('theme_id = :themeId AND name = :name', ['themeId' => $this->themeId, 'name' => $this->name]);

        try {
            self::executeQuery($query);
        } catch (PDOException $exception) {
            $errorInfo = $exception->errorInfo ?? ['', ''];
            if ($errorInfo[1] === 1062) {
                throw new UniqueFailedException($exception, self::getPDO());
            }

            if ($errorInfo[1] === 1452) {
                throw new ForeignKeyFailedException($exception, self::getPDO());
            }

            throw $exception;
        }
    }
}
