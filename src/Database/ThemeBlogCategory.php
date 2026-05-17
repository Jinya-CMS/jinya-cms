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
 * This class contains a blog category connected to a theme
 */
#[Table('theme_blog_category')]
class ThemeBlogCategory implements Creatable, Updatable, Deletable
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

    /** @var int The blog category ID */
    #[Column(sqlName: 'blog_category_id')]
    public int $blogCategoryId = -1;

    private static function getLinkIdProperty(): string
    {
        return 'blogCategoryId';
    }

    private static function getLinkIdColumn(): string
    {
        return 'blog_category_id';
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
