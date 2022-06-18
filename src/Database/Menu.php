<?php

namespace App\Database;

use App\Routing\Attributes\JinyaApi;
use App\Routing\Attributes\JinyaApiField;
use Iterator;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;

/**
 * This class contains a menu, menus contain menu items, which are used to create the navigation in themes
 */
#[JinyaApi]
class Menu extends Utils\LoadableEntity
{
    /** @var string The name of the menu, might be displayed in the frontend */
    #[JinyaApiField(required: true)]
    public string $name;
    /** @var int|null The ID of the file containing the logo */
    #[JinyaApiField]
    public ?int $logo = null;

    /**
     * Finds the menu with the matching ID
     *
     * @param int $id
     * @return Menu|null
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public static function findById(int $id): ?Menu
    {
        return self::fetchSingleById('menu', $id, new self());
    }

    /**
     * Finds all menus matching the keyword
     *
     * @param string $keyword
     * @return Iterator<Menu>
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public static function findByKeyword(string $keyword): Iterator
    {
        $sql = 'SELECT id, name, logo FROM menu WHERE name LIKE :keyword';
        try {
            return self::getPdo()->fetchIterator($sql, new self(), ['keyword' => "%$keyword%"]);
        } catch (InvalidQueryException$exception) {
            throw self::convertInvalidQueryExceptionToException($exception);
        }
    }

    /**
     * Finds all menus
     *
     * @return Iterator<Menu>
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public static function findAll(): Iterator
    {
        return self::fetchAllIterator('menu', new self());
    }

    /**
     * Formats the current menu into an array
     *
     * @return array<string, array<string, int|string>|int|string>
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public function format(): array
    {
        $logo = $this->getLogo();
        $logoData = [];
        if ($logo) {
            $logoData['id'] = $logo->getIdAsInt();
            $logoData['name'] = $logo->name;

            return [
                'name' => $this->name,
                'id' => $this->getIdAsInt(),
                'logo' => $logoData,
            ];
        }

        return [
            'name' => $this->name,
            'id' => $this->getIdAsInt(),
        ];
    }

    /**
     * Gets the logo file
     *
     * @return File|null
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public function getLogo(): ?File
    {
        if ($this->logo === null) {
            return null;
        }

        return File::findById($this->logo);
    }

    /**
     * Creates the current menu
     *
     * @return void
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function create(): void
    {
        $this->internalCreate('menu');
    }

    /**
     * Deletes the current menu
     *
     * @return void
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function delete(): void
    {
        $this->internalDelete('menu');
    }

    /**
     * Updates the current menu
     *
     * @return void
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function update(): void
    {
        $this->internalUpdate('menu');
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
        $sql = 'SELECT id, menu_id, parent_id, title, highlighted, position, artist_id, page_id, form_id, gallery_id, segment_page_id, category_id, route, blog_home_page FROM menu_item WHERE menu_id = :id ORDER BY position';

        try {
            return self::getPdo()->fetchIterator($sql, new MenuItem(), ['id' => $this->id]);
        } catch (InvalidQueryException$exception) {
            throw self::convertInvalidQueryExceptionToException($exception);
        }
    }
}
