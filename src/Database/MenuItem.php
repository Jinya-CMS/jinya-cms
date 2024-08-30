<?php

namespace Jinya\Cms\Database;

use Jinya\Cms\Database\Converter\BooleanConverter;
use Jinya\Cms\Database\Converter\NullableBooleanConverter;
use Iterator;
use Jinya\Database\Attributes\Column;
use Jinya\Database\Attributes\Id;
use Jinya\Database\Attributes\Table;
use Jinya\Database\EntityTrait;
use Jinya\Database\FindableEntityTrait;

/**
 * This class contains a menu item. A menu item is a class containing information about an entry in a menu.
 */
#[Table('menu_item')]
class MenuItem
{
    use EntityTrait;
    use FindableEntityTrait;

    #[Id]
    #[Column(autogenerated: true)]
    public int $id;

    #[Column]
    public int $position;

    /** @var int|null The ID of the menu this menu item belongs to. If the menu item has a menu item as parent, this value is null */
    #[Column(sqlName: 'menu_id')]
    public ?int $menuId = null;

    /** @var int|null The ID of the menu item, this menu item has as parent. If the menu item has a menu as parent, this value is null
     */
    #[Column(sqlName: 'parent_id')]
    public ?int $parentId = null;

    /** @var string The title of the menu item. This title is supposed to be displayed in the frontend */
    #[Column]
    public string $title;

    /**
     * @var string|null
     * The link of the menu item. This link can be either an absolute URL, a relative URL or null.
     * Absolute URLs open the link directly, while relative URLs are handled by the frontend controller and will render different entities.
     * Usually a null value indicates that the menu item is used as a parent item, which has no link but rather opens and shows the subitems
     */
    #[Column]
    public ?string $route = null;

    /** @var bool When true, the menu item should get a special highlighting in the frontend. Themes must respect the decision of the artist
     */
    #[Column]
    #[BooleanConverter]
    public bool $highlighted = false;

    /** @var int|null The ID of the form this menu item should render */
    #[Column(sqlName: 'form_id')]
    public ?int $formId = null;

    /** @var int|null The ID of the artist this menu item should render */
    #[Column(sqlName: 'artist_id')]
    public ?int $artistId = null;

    /** @var int|null The ID of the gallery this menu item should render */
    #[Column(sqlName: 'gallery_id')]
    public ?int $galleryId = null;

    /** @var int|null The ID of the segment page this menu item should render */
    #[Column(sqlName: 'segment_page_id')]
    public ?int $modernPageId = null;

    /** @var int|null The ID of the page this menu item should render */
    #[Column(sqlName: 'page_id')]
    public ?int $classicPageId = null;

    /** @var int|null The ID of the blog category this menu item should render */
    #[Column(sqlName: 'category_id')]
    public ?int $categoryId = null;

    /** @var bool|null If it is set to true, this menu item renders the blog home page */
    #[Column(sqlName: 'blog_home_page')]
    #[NullableBooleanConverter]
    public ?bool $blogHomePage = false;

    /**
     * Finds a menu item by its parent menu and the position
     *
     * @param int $menuId
     * @param int $position
     * @return MenuItem|null
     */
    public static function findByMenuAndPosition(int $menuId, int $position): ?MenuItem
    {
        $query = self::getQueryBuilder()
            ->newSelect()
            ->from(self::getTableName())
            ->cols([
                'id',
                'menu_id',
                'parent_id',
                'title',
                'highlighted',
                'position',
                'artist_id',
                'page_id',
                'form_id',
                'gallery_id',
                'segment_page_id',
                'category_id',
                'route',
                'blog_home_page'
            ])
            ->where('menu_id = :menuId AND position = :position', ['menuId' => $menuId, 'position' => $position]);

        /** @var array<string, mixed>[] $data */
        $data = self::executeQuery($query);

        if (empty($data)) {
            return null;
        }

        return self::fromArray($data[0]);
    }

    /**
     * Finds a menu item by its parent menu item and the position
     *
     * @param int $parentId
     * @param int $position
     * @return MenuItem|null
     */
    public static function findByMenuItemAndPosition(int $parentId, int $position): ?MenuItem
    {
        $query = self::getQueryBuilder()
            ->newSelect()
            ->from(self::getTableName())
            ->cols([
                'id',
                'menu_id',
                'parent_id',
                'title',
                'highlighted',
                'position',
                'artist_id',
                'page_id',
                'form_id',
                'gallery_id',
                'segment_page_id',
                'category_id',
                'route',
                'blog_home_page'
            ])
            ->where(
                'parent_id = :parentId AND position = :position',
                ['parentId' => $parentId, 'position' => $position]
            );

        /** @var array<string, mixed>[] $data */
        $data = self::executeQuery($query);

        if (empty($data)) {
            return null;
        }

        return self::fromArray($data[0]);
    }

    /**
     * Finds the menu item with the given route
     *
     * @param null|string $route
     * @return MenuItem|null
     */
    public static function findByRoute(?string $route): ?MenuItem
    {
        $query = self::getQueryBuilder()
            ->newSelect()
            ->from(self::getTableName())
            ->cols([
                'id',
                'menu_id',
                'parent_id',
                'title',
                'highlighted',
                'position',
                'artist_id',
                'page_id',
                'form_id',
                'gallery_id',
                'segment_page_id',
                'category_id',
                'route',
                'blog_home_page'
            ])
            ->where(
                'route = :route OR route = :routeWithTrailingSlash',
                ['route' => $route, 'routeWithTrailingSlash' => "/$route"]
            );

        /** @var array<string, mixed>[] $data */
        $data = self::executeQuery($query);

        if (empty($data)) {
            return null;
        }

        return self::fromArray($data[0]);
    }

    /**
     * Finds all menu items that are direct children of the given menu
     *
     * @param int $menuId
     * @return Iterator<MenuItem>
     */
    public static function findByMenu(int $menuId): Iterator
    {
        $query = self::getQueryBuilder()
            ->newSelect()
            ->from(self::getTableName())
            ->cols([
                'id',
                'menu_id',
                'parent_id',
                'title',
                'highlighted',
                'position',
                'artist_id',
                'page_id',
                'form_id',
                'gallery_id',
                'segment_page_id',
                'category_id',
                'route',
                'blog_home_page'
            ])
            ->where('menu_id = :menuId', ['menuId' => $menuId])
            ->orderBy(['position']);

        /** @var array<string, mixed>[] $data */
        $data = self::executeQuery($query);

        foreach ($data as $item) {
            yield self::fromArray($item);
        }
    }

    /**
     * Gets the parent
     *
     * @return MenuItem|null
     */
    public function getParent(): ?MenuItem
    {
        if ($this->parentId !== null) {
            return self::findById($this->parentId);
        }

        return null;
    }

    /**
     * Gets the menu
     *
     * @return Menu|null
     */
    public function getMenu(): ?Menu
    {
        if ($this->menuId !== null) {
            return Menu::findById($this->menuId);
        }

        return null;
    }

    /**
     * Formats the given menu item
     *
     * @return array<string, array<int|string, mixed>|bool|int|string|null>
     */
    public function format(): array
    {
        $data = [
            'id' => $this->id,
            'position' => $this->position,
            'highlighted' => $this->highlighted,
            'title' => $this->title,
            'route' => $this->route,
            'blogHomePage' => $this->blogHomePage,
            'items' => $this->formatIterator($this->getItems()),
        ];

        if (isset($this->formId)) {
            $form = $this->getForm();
            $data['form'] = [
                'id' => $form?->id,
                'title' => $form?->title,
            ];
        } elseif (isset($this->artistId)) {
            $artist = $this->getArtist();
            $data['artist'] = [
                'id' => $artist?->id,
                'artistName' => $artist?->artistName,
                'email' => $artist?->email,
            ];
        } elseif (isset($this->classicPageId)) {
            $classicPage = $this->getClassicPage();
            $data['classicPage'] = [
                'id' => $classicPage?->id,
                'title' => $classicPage?->title,
            ];
        } elseif (isset($this->modernPageId)) {
            $modernPage = $this->getModernPage();
            $data['modernPage'] = [
                'id' => $modernPage?->id,
                'name' => $modernPage?->name,
            ];
        } elseif (isset($this->galleryId)) {
            $gallery = $this->getGallery();
            $data['gallery'] = [
                'id' => $gallery?->id,
                'name' => $gallery?->name,
            ];
        } elseif (isset($this->categoryId)) {
            $category = $this->getBlogCategory();
            $data['blogCategory'] = [
                'id' => $category?->id,
                'name' => $category?->name,
            ];
        }

        return $data;
    }

    /**
     * Gets the associated form
     *
     * @return Form|null
     */
    public function getForm(): ?Form
    {
        if ($this->formId === null) {
            return null;
        }

        return Form::findById($this->formId);
    }

    /**
     * Gets the associated artist
     *
     * @return Artist|null
     */
    public function getArtist(): ?Artist
    {
        if ($this->artistId === null) {
            return null;
        }

        return Artist::findById($this->artistId);
    }

    /**
     * Gets the associated page
     *
     * @return ClassicPage|null
     */
    public function getClassicPage(): ?ClassicPage
    {
        if ($this->classicPageId === null) {
            return null;
        }

        return ClassicPage::findById($this->classicPageId);
    }

    /**
     * Gets the associated segment page
     *
     * @return ModernPage|null
     */
    public function getModernPage(): ?ModernPage
    {
        if ($this->modernPageId === null) {
            return null;
        }

        return ModernPage::findById($this->modernPageId);
    }

    /**
     * Gets the associated gallery
     *
     * @return Gallery|null
     */
    public function getGallery(): ?Gallery
    {
        if ($this->galleryId === null) {
            return null;
        }

        return Gallery::findById($this->galleryId);
    }

    /**
     * Gets the associated category
     *
     * @return BlogCategory|null
     */
    public function getBlogCategory(): ?BlogCategory
    {
        if ($this->categoryId === null) {
            return null;
        }

        return BlogCategory::findById($this->categoryId);
    }

    /**
     * Format an iterator
     *
     * @param Iterator $iterator
     * @return array<int, mixed>
     */
    private function formatIterator(Iterator $iterator): array
    {
        $data = [];
        foreach ($iterator as $item) {
            /* @var $item MenuItem */
            $data[] = $item->format();
        }

        return $data;
    }

    /**
     *  Gets the menu items
     * @return Iterator<MenuItem>
     */
    public function getItems(): Iterator
    {
        return self::findByParent($this->id);
    }

    /**
     * Finds all menu items that are direct children of the given menu item
     *
     * @param int $parentId
     * @return Iterator<MenuItem>
     */
    public static function findByParent(int $parentId): Iterator
    {
        $query = self::getQueryBuilder()
            ->newSelect()
            ->from(self::getTableName())
            ->cols([
                'id',
                'menu_id',
                'parent_id',
                'title',
                'highlighted',
                'position',
                'artist_id',
                'page_id',
                'form_id',
                'gallery_id',
                'segment_page_id',
                'category_id',
                'route',
                'blog_home_page'
            ])
            ->where('parent_id = :parentId', ['parentId' => $parentId])
            ->orderBy(['position']);

        /** @var array<string, mixed>[] $data */
        $data = self::executeQuery($query);

        foreach ($data as $item) {
            yield self::fromArray($item);
        }
    }
}
