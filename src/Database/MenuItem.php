<?php

namespace App\Database;

use Exception;
use Iterator;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;
use Laminas\Hydrator\Strategy\BooleanStrategy;
use LogicException;
use RuntimeException;

class MenuItem extends Utils\RearrangableEntity
{
    public ?int $menuId = null;
    public ?int $parentId = null;
    public string $title;
    public ?string $route = null;
    public bool $highlighted = false;
    public ?int $formId = null;
    public ?int $artistId = null;
    public ?int $galleryId = null;
    public ?int $segmentPageId = null;
    public ?int $pageId = null;
    public ?int $categoryId = null;
    public ?bool $blogHomePage = false;

    /**
     * {@inheritDoc}
     */
    public static function findByKeyword(string $keyword): Iterator
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * {@inheritDoc}
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
     * @throws Exceptions\ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     * @throws NoResultException
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
     *
     * @return MenuItem|null
     *
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
     * {@inheritDoc}
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
     * {@inheritDoc}
     * @throws Exception
     */
    public function create(): void
    {
        $this->rearrange($this->position);
        $this->internalCreate('menu_item', ['highlighted' => new BooleanStrategy(1, 0), 'blogHomePage' => new BooleanStrategy(1, 0)]);
    }

    /**
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
     * {@inheritDoc}
     */
    public function update(bool $rearrange = true): void
    {
        if ($rearrange) {
            $this->rearrange($this->position);
        }

        $this->internalUpdate('menu_item', ['highlighted' => new BooleanStrategy(1, 0), 'blogHomePage' => new BooleanStrategy(1, 0)]);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(): void
    {
        $this->internalDelete('menu_item');
        $this->rearrange(-1);
    }

    /**
     * {@inheritDoc}
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
