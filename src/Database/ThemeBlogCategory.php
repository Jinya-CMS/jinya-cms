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
use Jinya\Database\Exception\ForeignKeyFailedException;
use Jinya\Database\Exception\UniqueFailedException;
use Jinya\Database\Updatable;
use PDOException;

/**
 * This class contains a blog category connected to a theme
 */
#[Table('theme_blog_category')]
class ThemeBlogCategory implements Creatable, Updatable, Deletable
{
    use EntityTrait;
    use DeletableEntityTrait;

    /** @var string The theme name */
    #[Column]
    public string $name = '';

    /** @var int The theme id */
    #[Column(sqlName: 'theme_id')]
    public int $themeId = -1;

    /** @var int The blog category ID */
    #[Column(sqlName: 'blog_category_id')]
    public int $blogCategoryId = -1;

    /**
     * Finds a blog category by name and theme
     *
     * @param int $themeId
     * @param string $name
     * @return ThemeBlogCategory|null
     */
    public static function findByThemeAndName(int $themeId, string $name): ThemeBlogCategory|null
    {
        $query = self::getQueryBuilder()
            ->newSelect()
            ->from(self::getTableName())
            ->cols([
                'theme_id',
                'name',
                'blog_category_id',
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
     * Finds all theme blog categories in the theme with the given ID
     *
     * @param int $themeId
     * @return Iterator<ThemeBlogCategory>
     */
    public static function findByTheme(int $themeId): Iterator
    {
        $query = self::getQueryBuilder()
            ->newSelect()
            ->from(self::getTableName())
            ->cols([
                'theme_id',
                'name',
                'blog_category_id',
            ])
            ->where('theme_id = :themeId', ['themeId' => $themeId]);

        /** @var array<string, mixed>[] $data */
        $data = self::executeQuery($query);
        foreach ($data as $item) {
            yield self::fromArray($item);
        }
    }

    /**
     * Creates the current theme blog category
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
                'blog_category_id' => $this->blogCategoryId,
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
            ->set('blog_category_id', $this->blogCategoryId)
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

    /**
     * Formats the theme blog category into an array
     *
     * @return array<string, array<string, mixed>|string|null>
     */
    #[ArrayShape(['name' => 'string', 'blogCategory' => 'array'])]
    public function format(): array
    {
        return [
            'name' => $this->name,
            'blogCategory' => $this->getBlogCategory()?->format(),
        ];
    }

    /**
     * Gets the blog category associated to this theme blog category
     *
     * @return BlogCategory|null
     */
    public function getBlogCategory(): BlogCategory|null
    {
        return BlogCategory::findById($this->blogCategoryId);
    }
}
