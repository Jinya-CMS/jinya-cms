<?php

namespace App\Database;

use App\OpenApiGeneration\Attributes\OpenApiField;
use App\OpenApiGeneration\Attributes\OpenApiHiddenField;
use App\OpenApiGeneration\Attributes\OpenApiModel;
use App\OpenApiGeneration\Attributes\OpenApiRecursiveField;
use Exception;
use Iterator;
use RuntimeException;

#[OpenApiModel('A menu item contains the navigation information')]
#[OpenApiRecursiveField('items')]
class MenuItem extends Utils\RearrangableEntity implements Utils\FormattableEntityInterface
{
    #[OpenApiHiddenField]
    public ?int $menuId = null;
    #[OpenApiHiddenField]
    public ?int $parentId = null;
    #[OpenApiField(required: true)]
    public string $title;
    #[OpenApiField(required: false, defaultValue: null)]
    public ?string $route;
    #[OpenApiField(required: false)]
    public bool $highlighted = false;
    #[OpenApiField(required: false, structure: [
        'id' => ['type' => 'integer'],
        'title' => ['type' => 'string'],
    ], name: 'form')]
    public ?int $formId = null;
    #[OpenApiField(required: false, structure: [
        'id' => ['type' => 'integer'],
        'artistName' => ['type' => 'string'],
        'email' => ['type' => 'string', 'format' => 'email'],
    ], name: 'artist')]
    public ?int $artistId = null;
    #[OpenApiField(required: false, structure: [
        'id' => ['type' => 'integer'],
        'name' => ['type' => 'string'],
    ], name: 'gallery')]
    public ?int $galleryId = null;
    #[OpenApiField(required: false, structure: [
        'id' => ['type' => 'integer'],
        'name' => ['type' => 'string'],
    ], name: 'segmentPage')]
    public ?int $segmentPageId = null;
    #[OpenApiField(required: false, structure: [
        'id' => ['type' => 'integer'],
        'title' => ['type' => 'string'],
    ], name: 'page')]
    public ?int $pageId = null;

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
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    public static function findByMenuAndPosition(int $menuId, int $position): ?MenuItem
    {
        $sql = 'SELECT id, menu_id, parent_id, title, highlighted, position, artist_id, page_id, form_id, gallery_id, segment_page_id, route FROM menu_item WHERE menu_id = :id AND position = :position';

        $result = self::executeStatement(
            $sql,
            [
                'id' => $menuId,
                'position' => $position,
            ]
        );

        if (0 === count($result)) {
            return null;
        }

        /* @noinspection PhpIncompatibleReturnTypeInspection */
        return self::hydrateSingleResult($result[0], new self());
    }

    /**
     * Finds a menu item by its parent menu item and the position
     *
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     * @noinspection PhpIncompatibleReturnTypeInspection
     */
    public static function findByMenuItemAndPosition(int $menuItemId, int $position): ?MenuItem
    {
        $sql = 'SELECT id, menu_id, parent_id, title, highlighted, position, artist_id, page_id, form_id, gallery_id, segment_page_id, route FROM menu_item WHERE parent_id = :id AND position = :position';

        $result =
            self::executeStatement(
                $sql,
                [
                    'id' => $menuItemId,
                    'position' => $position,
                ]
            );

        if (0 === count($result)) {
            return null;
        }

        return self::hydrateSingleResult($result[0], new self());
    }

    /**
     * Finds the menu item with the given route
     *
     * @param $route
     * @return MenuItem|null
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     * @noinspection PhpIncompatibleReturnTypeInspection
     */
    public static function findByRoute($route): ?MenuItem
    {
        $sql = 'SELECT id, menu_id, parent_id, title, highlighted, position, artist_id, page_id, form_id, gallery_id, segment_page_id, route FROM menu_item WHERE route = :route OR route = :routeWithTrailingSlash';
        $result =
            self::executeStatement(
                $sql,
                [
                    'route' => $route,
                    'routeWithTrailingSlash' => "/$route",
                ]
            );
        if (0 === count($result)) {
            return null;
        }

        return self::hydrateSingleResult($result[0], new self());
    }

    /**
     * Gets the parent
     *
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    public function getParent(): ?MenuItem
    {
        return self::findById($this->parentId);
    }

    /**
     * {@inheritDoc}
     * @return MenuItem
     */
    public static function findById(int $id): ?object
    {
        return self::fetchSingleById('menu_item', $id, new self());
    }

    /**
     * Gets the menu
     *
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    public function getMenu(): ?Menu
    {
        return Menu::findById($this->parentId);
    }

    /**
     * {@inheritDoc}
     * @throws Exception
     */
    public function create(): void
    {
        $this->rearrange($this->position);
        $this->internalCreate('menu_item');
    }

    private function rearrange(int $position): void
    {
        if (null !== $this->menuId) {
            $this->internalRearrange('menu_item', 'menu_id', $this->menuId, $position);
        } else {
            $this->internalRearrange('menu_item', 'parent_id', $this->parentId, $position);
        }
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
    public function update(bool $rearrange = true): void
    {
        if ($rearrange) {
            $this->rearrange($this->position);
        }

        $this->internalUpdate('menu_item');
    }

    /**
     * {@inheritDoc}
     */
    public function move(int $newPosition): void
    {
        $this->rearrange($newPosition);
        parent::move($newPosition);
    }

    /**
     * Formats the menu items below this recursive
     *
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
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
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
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
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    public function getForm(): ?Form
    {
        if (null === $this->formId) {
            return null;
        }

        return Form::findById($this->formId);
    }

    /**
     * Gets the associated artist
     *
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    public function getArtist(): ?Artist
    {
        if (null === $this->artistId) {
            return null;
        }

        return Artist::findById($this->artistId);
    }

    /**
     * Gets the associated page
     *
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    public function getPage(): ?SimplePage
    {
        if (null === $this->pageId) {
            return null;
        }

        return SimplePage::findById($this->pageId);
    }

    /**
     * Gets the associated segment page
     *
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    public function getSegmentPage(): ?SegmentPage
    {
        if (null === $this->segmentPageId) {
            return null;
        }

        return SegmentPage::findById($this->segmentPageId);
    }

    /**
     * Gets the associated gallery
     *
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    public function getGallery(): ?Gallery
    {
        if (null === $this->galleryId) {
            return null;
        }

        return Gallery::findById($this->galleryId);
    }

    /**
     * Format the artist list
     *
     * @param Iterator $iterator
     * @return array
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
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
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    public function getItems(): Iterator
    {
        $sql = 'SELECT id, menu_id, parent_id, title, highlighted, position, artist_id, page_id, form_id, gallery_id, segment_page_id, route FROM menu_item WHERE parent_id = :id ORDER BY position';

        $result = self::executeStatement($sql, ['id' => $this->id]);

        return self::hydrateMultipleResults($result, new self());
    }
}
