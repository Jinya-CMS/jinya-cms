<?php

namespace App\Database;

use Iterator;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;
use Laminas\Hydrator\Strategy\BooleanStrategy;
use LogicException;
use RuntimeException;

/**
 * This class contains a menu item. A menu item is a class containing information about an entry in a menu.
 */
class MenuItem extends Utils\RearrangableEntity
{
    /** @var int|null The ID of the menu this menu item belongs to. If the menu item has a menu item as parent this value is null */
    public ?int $menuId = null;
    /** @var int|null The ID of the menu item, this menu item has as parent. If the menu item has a menu as parent this value is null */
    public ?int $parentId = null;
    /** @var string The title of the menu item. This title is supposed to be displayed in the frontend */
    public string $title;
    /**
     * @var string|null
     * The link of the menu item. This link can be either an absolute URL, a relative URL or null.
     * Absolute URLs open the link directly, while relative URLs are handled by the frontend controller and will render different entities.
     * Usually a null value indicates that the menu item is used as a parent item, which has no link but rather opens and shows the sub items
     */
    public ?string $route = null;
    /** @var bool When true the menu item should get a special highlighting in the frontend. Themes must respect the decision of the artist */
    public bool $highlighted = false;
    /** @var int|null The ID of the form this menu item should render */
    public ?int $formId = null;
    /** @var int|null The ID of the artist this menu item should render */
    public ?int $artistId = null;
    /** @var int|null The ID of the gallery this menu item should render */
    public ?int $galleryId = null;
    /** @var int|null The ID of the segment page this menu item should render */
    public ?int $segmentPageId = null;
    /** @var int|null The ID of the page this menu item should render */
    public ?int $pageId = null;
    /** @var int|null The ID of the blog category this menu item should render */
    public ?int $categoryId = null;
    /** @var bool|null If it is set to true, this menu item renders the blog home page */
    public ?bool $blogHomePage = false;

    /**
     * Not implemented
     */
    public static function findByKeyword(string $keyword): Iterator
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * Not implemented
     */
    public static function findAll(): Iterator
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * Finds a menu item by its parent menu and the position
     *
     * @param int $menuId
     * @param int $position
     * @return MenuItem|null
     * @throws Exceptions\ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     * @throws NoResultException
     * @throws NoResultException
     */
    public static function findByMenuAndPosition(int $menuId, int $position): ?MenuItem
    {
        $sql = 'SELECT id, menu_id, parent_id, title, highlighted, position, artist_id, page_id, form_id, gallery_id, category_id, segment_page_id, route, blog_home_page FROM menu_item WHERE menu_id = :id AND position = :position';

        try {
            return self::getPdo()->fetchObject($sql, new self(), [
                'id' => $menuId,
                'position' => $position,
            ], ['highlighted' => new BooleanStrategy(1, 0)]);
        } catch (InvalidQueryException$exception) {
            throw self::convertInvalidQueryExceptionToException($exception);
        }
    }

    /**
     * Finds a menu item by its parent menu item and the position
     *
     * @param int $menuItemId
     * @param int $position
     * @return MenuItem|null
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public static function findByMenuItemAndPosition(int $menuItemId, int $position): ?MenuItem
    {
        $sql = 'SELECT id, menu_id, parent_id, title, highlighted, position, artist_id, page_id, form_id, gallery_id, category_id, segment_page_id, route, blog_home_page FROM menu_item WHERE parent_id = :id AND position = :position';

        try {
            return self::getPdo()->fetchObject($sql, new self(), [
                'id' => $menuItemId,
                'position' => $position,
            ], ['highlighted' => new BooleanStrategy(1, 0)]);
        } catch (InvalidQueryException$exception) {
            throw self::convertInvalidQueryExceptionToException($exception);
        }
    }

    /**
     * Finds the menu item with the given route
     *
     * @param null|string $route
     * @return MenuItem|null
     * @throws Exceptions\ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     * @throws NoResultException
     * @throws NoResultException
     */
    public static function findByRoute(?string $route): ?MenuItem
    {
        $sql = 'SELECT id, menu_id, parent_id, title, highlighted, position, artist_id, page_id, form_id, gallery_id, category_id, segment_page_id, route, blog_home_page FROM menu_item WHERE route = :route OR route = :routeWithTrailingSlash';

        try {
            return self::getPdo()->fetchObject($sql, new self(), [
                'route' => $route,
                'routeWithTrailingSlash' => "/$route",
            ], ['highlighted' => new BooleanStrategy(1, 0)]);
        } catch (InvalidQueryException$exception) {
            throw self::convertInvalidQueryExceptionToException($exception);
        }
    }

    /**
     * Gets the parent
     *
     * @return MenuItem|null
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public function getParent(): ?MenuItem
    {
        if ($this->parentId !== null) {
            return self::findById($this->parentId);
        }

        return null;
    }

    /**
     * Finds the menu item matching the ID
     *
     * @param int $id
     * @return MenuItem|null
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public static function findById(int $id): ?MenuItem
    {
        return self::fetchSingleById('menu_item', $id, new self(), ['highlighted' => new BooleanStrategy(1, 0)]);
    }

    /**
     * Gets the menu
     *
     * @return Menu|null
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public function getMenu(): ?Menu
    {
        if ($this->menuId !== null) {
            return Menu::findById($this->menuId);
        }

        return null;
    }

    /**
     * Creates the current menu item, also moves the position of the other menu items according to the new position
     *
     * @return void
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function create(): void
    {
        $this->rearrange($this->position);
        $this->internalCreate('menu_item', ['highlighted' => new BooleanStrategy(1, 0), 'blogHomePage' => new BooleanStrategy(1, 0)]);
    }

    /**
     * Moves the current menu item. Depending on the parent it will rearrange the positions and move the menu items in the correct order
     *
     * @param int $position
     * @return void
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    private function rearrange(int $position): void
    {
        if ($this->menuId !== null) {
            $this->internalRearrange('menu_item', 'menu_id', $this->menuId, $position);
            $this->update(false);
            $this->resetOrder('menu_item', 'menu_id', $this->menuId);
        } elseif ($this->parentId !== null) {
            $this->internalRearrange('menu_item', 'parent_id', $this->parentId, $position);
            $this->update(false);
            $this->resetOrder('menu_item', 'parent_id', $this->parentId);
        } else {
            throw new LogicException('No parent provided');
        }
    }

    /**
     * Updates the menu item
     *
     * @param bool $rearrange If true, the menu items on the same level will be rearranged in hierarchy.
     * @return void
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function update(bool $rearrange = true): void
    {
        if ($rearrange) {
            $this->rearrange($this->position);
        }

        $this->internalUpdate('menu_item', ['highlighted' => new BooleanStrategy(1, 0), 'blogHomePage' => new BooleanStrategy(1, 0)]);
    }

    /**
     * Deletes the current menu item, the order of the remaining items will be reset
     *
     * @return void
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function delete(): void
    {
        $this->internalDelete('menu_item');
        $this->rearrange(-1);
    }

    /**
     * Moves the current menu item to the new position. All other menu items are rearranged accordingly
     *
     * @param int $newPosition
     * @return void
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function move(int $newPosition): void
    {
        $this->rearrange($newPosition);
    }

    /**
     * Formats the menu items below this recursive
     *
     * @return array<string, array<string>|bool|int|string|null>
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public function formatRecursive(): array
    {
        $result = $this->format();
        $result['items'] = $this->formatIterator($this->getItems());

        return $result;
    }

    /**
     * Formats the given menu item
     *
     * @return array<string, array<string, int|string|null>|bool|int|string|null>
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public function format(): array
    {
        $data = [
            'id' => $this->getIdAsInt(),
            'position' => $this->position,
            'highlighted' => $this->highlighted,
            'title' => $this->title,
            'route' => $this->route,
            'blogHomePage' => $this->blogHomePage,
        ];

        if (isset($this->formId)) {
            $form = $this->getForm();
            $data['form'] = [
                'id' => $form?->getIdAsInt(),
                'title' => $form?->title,
            ];
        } elseif (isset($this->artistId)) {
            $artist = $this->getArtist();
            $data['artist'] = [
                'id' => $artist?->getIdAsInt(),
                'artistName' => $artist?->artistName,
                'email' => $artist?->email,
            ];
        } elseif (isset($this->pageId)) {
            $page = $this->getPage();
            $data['page'] = [
                'id' => $page?->getIdAsInt(),
                'title' => $page?->title,
            ];
        } elseif (isset($this->segmentPageId)) {
            $segmentPage = $this->getSegmentPage();
            $data['segmentPage'] = [
                'id' => $segmentPage?->getIdAsInt(),
                'name' => $segmentPage?->name,
            ];
        } elseif (isset($this->galleryId)) {
            $gallery = $this->getGallery();
            $data['gallery'] = [
                'id' => $gallery?->getIdAsInt(),
                'name' => $gallery?->name,
            ];
        } elseif (isset($this->categoryId)) {
            $category = $this->getBlogCategory();
            $data['category'] = [
                'id' => $category?->getIdAsInt(),
                'name' => $category?->name,
            ];
        }

        return $data;
    }

    /**
     * Gets the associated form
     *
     * @return Form|null
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
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
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
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
     * @return SimplePage|null
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public function getPage(): ?SimplePage
    {
        if ($this->pageId === null) {
            return null;
        }

        return SimplePage::findById($this->pageId);
    }

    /**
     * Gets the associated segment page
     *
     * @return SegmentPage|null
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public function getSegmentPage(): ?SegmentPage
    {
        if ($this->segmentPageId === null) {
            return null;
        }

        return SegmentPage::findById($this->segmentPageId);
    }

    /**
     * Gets the associated gallery
     *
     * @return Gallery|null
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
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
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public function getBlogCategory(): ?BlogCategory
    {
        if ($this->categoryId === null) {
            return null;
        }

        return BlogCategory::findById($this->categoryId);
    }

    /**
     * Format the artist list
     *
     * @param Iterator $iterator
     * @return array<int, mixed>
     * @throws Exceptions\ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     * @throws NoResultException
     */
    private function formatIterator(Iterator $iterator): array
    {
        $data = [];
        foreach ($iterator as $item) {
            /* @var $item MenuItem */
            $data[] = $item->formatRecursive();
        }

        return $data;
    }

    /**
     * Gets the menu items
     *
     * @throws Exceptions\ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    public function getItems(): Iterator
    {
        $sql = 'SELECT id, menu_id, parent_id, title, highlighted, position, artist_id, page_id, form_id, gallery_id, category_id, segment_page_id, route, blog_home_page FROM menu_item WHERE parent_id = :id ORDER BY position';

        try {
            return self::getPdo()->fetchIterator($sql, new self(), ['id' => $this->id]);
        } catch (InvalidQueryException$exception) {
            throw self::convertInvalidQueryExceptionToException($exception);
        }
    }
}
