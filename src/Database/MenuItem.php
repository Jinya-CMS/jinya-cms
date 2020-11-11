<?php

namespace App\Database;

use Exception;
use Iterator;
use Laminas\Db\Sql\Predicate\PredicateSet;
use RuntimeException;

class MenuItem extends Utils\RearrangableEntity implements Utils\FormattableEntityInterface
{
    public ?int $menuId = null;
    public ?int $parentId = null;
    public string $title;
    public ?string $route;
    public bool $highlighted = false;
    public ?int $formId = null;
    public ?int $artistId = null;
    public ?int $galleryId = null;
    public ?int $segmentPageId = null;
    public ?int $pageId = null;

    /**
     * @inheritDoc
     */
    public static function findByKeyword(string $keyword): Iterator
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * @inheritDoc
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
     * @return MenuItem
     */
    public static function findByMenuAndPosition(int $menuId, int $position): ?MenuItem
    {
        $sql = self::getSql();
        $select = $sql
            ->select()
            ->from('menu_item')
            ->where(['menu_id = :id', 'position = :position'], PredicateSet::OP_AND);

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return self::hydrateSingleResult($sql->prepareStatementForSqlObject($select)->execute([
            'id' => $menuId,
            'position' => $position
        ]), new self());
    }

    /**
     * Finds a menu item by its parent menu item and the position
     *
     * @param int $menuItemId
     * @param int $position
     * @return MenuItem
     */
    public static function findByMenuItemAndPosition(int $menuItemId, int $position): ?MenuItem
    {
        $sql = self::getSql();
        $select = $sql
            ->select()
            ->from('menu_item')
            ->where(['parent_id = :id', 'position = :position'], PredicateSet::OP_AND);

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return self::hydrateSingleResult($sql->prepareStatementForSqlObject($select)->execute([
            'id' => $menuItemId,
            'position' => $position
        ]), new self());
    }

    /**
     * Finds the menu item with the given route
     *
     * @param $route
     * @return MenuItem
     */
    public static function findByRoute($route): ?MenuItem
    {
        $sql = self::getSql();
        $select = $sql
            ->select()
            ->from('menu_item')
            ->where(['route = :route', 'route = :routeWithTrailingSlash'], PredicateSet::OP_OR);

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return self::hydrateSingleResult($sql->prepareStatementForSqlObject($select)->execute([
            'route' => $route,
            'routeWithTrailingSlash' => "/$route",
        ]), new self());
    }

    /**
     * Gets the parent
     *
     * @return MenuItem|null
     */
    public function getParent(): ?MenuItem
    {
        return self::findById($this->parentId);
    }

    /**
     * @inheritDoc
     * @return MenuItem
     */
    public static function findById(int $id)
    {
        return self::fetchSingleById('menu_item', $id, new self());
    }

    /**
     * Gets the menu
     *
     * @return Menu|null
     */
    public function getMenu(): ?Menu
    {
        return Menu::findById($this->parentId);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function create(): void
    {
        $this->rearrange($this->position);
        $this->internalCreate('menu_item');
    }

    private function rearrange(int $position): void
    {
        if ($this->menuId !== null) {
            $this->internalRearrange('menu_item', 'menu_id', $this->menuId, $position);
        } else {
            $this->internalRearrange('menu_item', 'parent_id', $this->parentId, $position);
        }
    }

    /**
     * @inheritDoc
     */
    public function delete(): void
    {
        $this->internalDelete('menu_item');
        $this->rearrange(-1);
    }

    /**
     * @inheritDoc
     */
    public function update(bool $rearrange = true): void
    {
        if ($rearrange) {
            $this->rearrange($this->position);
        }

        $this->internalUpdate('menu_item');
    }

    /**
     * @inheritDoc
     */
    public function move(int $newPosition): void
    {
        $this->rearrange($newPosition);
        parent::move($newPosition);
    }

    /**
     * Formats the menu items below this recursive
     *
     * @return array
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
     * @return array
     * @noinspection NullPointerExceptionInspection
     */
    public function format(): array
    {
        $data = [
            'id' => $this->getIdAsInt(),
            'position' => $this->position,
            'highlighted' => $this->highlighted,
            'title' => $this->title,
            'route' => $this->route,
        ];

        if (isset($this->formId)) {
            $form = $this->getForm();
            $data['form'] = [
                'id' => $form->getIdAsInt(),
                'title' => $form->title,
            ];
        } elseif (isset($this->artistId)) {
            $artist = $this->getArtist();
            $data['artist'] = [
                'id' => $artist->getIdAsInt(),
                'artistName' => $artist->artistName,
                'email' => $artist->email,
            ];
        } elseif (isset($this->pageId)) {
            $page = $this->getPage();
            $data['page'] = [
                'id' => $page->getIdAsInt(),
                'title' => $page->title,
            ];
        } elseif (isset($this->segmentPageId)) {
            $segmentPage = $this->getSegmentPage();
            $data['segmentPage'] = [
                'id' => $segmentPage->getIdAsInt(),
                'name' => $segmentPage->name,
            ];
        } elseif (isset($this->galleryId)) {
            $gallery = $this->getGallery();
            $data['gallery'] = [
                'id' => $gallery->getIdAsInt(),
                'name' => $gallery->name,
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
     * @return SimplePage|null
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
     */
    public function getGallery(): ?Gallery
    {
        if ($this->galleryId === null) {
            return null;
        }

        return Gallery::findById($this->galleryId);
    }

    /**
     * Format the artist list
     *
     * @param Iterator $iterator
     * @return array
     */
    private function formatIterator(Iterator $iterator): array
    {
        $data = [];
        foreach ($iterator as $item) {
            /** @var $item MenuItem */
            $data[] = $item->formatRecursive();
        }

        return $data;
    }

    /**
     * Gets the menu items
     *
     * @return Iterator
     */
    public function getItems(): Iterator
    {
        $sql = self::getSql();
        $select = $sql
            ->select()
            ->from('menu_item')
            ->where(['parent_id = :id'])
            ->order('position ASC');

        $result = self::executeStatement($sql->prepareStatementForSqlObject($select), ['id' => $this->id]);

        return self::hydrateMultipleResults($result, new self());
    }
}