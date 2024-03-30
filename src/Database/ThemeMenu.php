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
 * This class contains a menu connected to a theme
 */
#[Table('theme_menu')]
class ThemeMenu implements Creatable, Updatable, Deletable
{
    use EntityTrait;
    use DeletableEntityTrait;

    /** @var string The theme name */
    #[Column]
    public string $name = '';

    /** @var int The theme id */
    #[Column(sqlName: 'theme_id')]
    public int $themeId = -1;

    /** @var int The menu ID */
    #[Column(sqlName: 'menu_id')]
    public int $menuId = -1;

    /**
     * Finds a menu by name and theme
     *
     * @param int $themeId
     * @param string $name
     * @return ThemeMenu|null
     */
    public static function findByThemeAndName(int $themeId, string $name): ?ThemeMenu
    {
        $query = self::getQueryBuilder()
            ->newSelect()
            ->from(self::getTableName())
            ->cols([
                'theme_id',
                'name',
                'menu_id',
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
     * Finds the menus for the given theme
     *
     * @param int $themeId
     * @return Iterator<ThemeMenu>
     */
    public static function findByTheme(int $themeId): Iterator
    {
        $query = self::getQueryBuilder()
            ->newSelect()
            ->from(self::getTableName())
            ->cols([
                'theme_id',
                'name',
                'menu_id',
            ])
            ->where('theme_id = :themeId', ['themeId' => $themeId]);

        /** @var array<string, mixed>[] $data */
        $data = self::executeQuery($query);
        foreach ($data as $item) {
            yield self::fromArray($item);
        }
    }

    /**
     * Formats the theme menu into an array
     *
     * @return array<string, array<string, array<string, int|string>|int|string>|string|null>
     */
    #[ArrayShape(['name' => 'string', 'menu' => 'array'])] public function format(): array
    {
        return [
            'name' => $this->name,
            'menu' => $this->getMenu()?->format(),
        ];
    }

    /**
     * Gets the menu of the theme menu
     *
     * @return Menu|null
     */
    public function getMenu(): ?Menu
    {
        return Menu::findById($this->menuId);
    }

    /**
     * Creates the current theme menu
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
                'menu_id' => $this->menuId,
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
            ->set('menu_id', $this->menuId)
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
