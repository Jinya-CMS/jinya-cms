<?php

namespace App\Database;

use Exception;
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
 * This class contains a file connected to a theme
 */
#[Table('theme_file')]
class ThemeFile implements Creatable, Updatable, Deletable
{
    use EntityTrait;
    use DeletableEntityTrait;

    /** @var string The theme name */
    #[Column]
    public string $name = '';

    /** @var int The theme id */
    #[Column(sqlName: 'theme_id')]
    public int $themeId = -1;

    /** @var int The file ID */
    #[Column(sqlName: 'file_id')]
    public int $fileId = -1;

    /**
     * Finds a file by name and theme
     *
     * @param int $themeId
     * @param string $name
     * @return ThemeFile|null
     */
    public static function findByThemeAndName(int $themeId, string $name): ?ThemeFile
    {
        $query = self::getQueryBuilder()
            ->newSelect()
            ->from(self::getTableName())
            ->cols([
                'theme_id',
                'name',
                'file_id',
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
     * Finds the files for the given theme
     *
     * @param int $themeId
     * @return Iterator<ThemeFile>
     */
    public static function findByTheme(int $themeId): Iterator
    {
        $query = self::getQueryBuilder()
            ->newSelect()
            ->from(self::getTableName())
            ->cols([
                'theme_id',
                'name',
                'file_id',
            ])
            ->where('theme_id = :themeId', ['themeId' => $themeId]);

        /** @var array<string, mixed>[] $data */
        $data = self::executeQuery($query);
        foreach ($data as $item) {
            yield self::fromArray($item);
        }
    }

    /**
     * Formats the theme file into an array
     *
     * @return array{'name': string, 'file': array<string, mixed>|null}
     * @throws Exception
     */
    #[ArrayShape(['name' => 'string', 'file' => 'array'])] public function format(): array
    {
        return [
            'name' => $this->name,
            'file' => $this->getFile()?->format(),
        ];
    }

    /**
     * Gets the file of the theme file
     *
     * @return File|null
     *
     */
    public function getFile(): ?File
    {
        return File::findById($this->fileId);
    }

    /**
     * Create the current theme file
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
                'file_id' => $this->fileId,
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
            /** @phpstan-ignore-next-line */
            ->set('file_id', $this->fileId)
            ->where('theme_id = :themeId AND name = :name', ['themeId' => $this->themeId, 'name' => $this->name]);

        self::executeQuery($query);
    }
}
