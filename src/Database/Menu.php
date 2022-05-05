<?php

namespace App\Database;

use App\Routing\Attributes\JinyaApi;
use App\Routing\Attributes\JinyaApiField;
use Exception;
use Iterator;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;

#[JinyaApi]
class Menu extends Utils\LoadableEntity implements Utils\FormattableEntityInterface
{
    #[JinyaApiField(required: true)]
    public string $name;
    #[JinyaApiField]
    public ?int $logo = null;

    /**
     * {@inheritDoc}
     * @param int $id
     * @return object|null
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public static function findById(int $id): ?object
    {
        return self::fetchSingleById('menu', $id, new self());
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public static function findAll(): Iterator
    {
        return self::fetchAllIterator('menu', new self());
    }

    /**
     * @return array
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
        if (null === $this->logo) {
            return null;
        }

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return File::findById($this->logo);
    }

    /**
     * {@inheritDoc}
     * @throws Exception
     */
    public function create(): void
    {
        $this->internalCreate('menu');
    }

    /**
     * {@inheritDoc}
     */
    public function delete(): void
    {
        $this->internalDelete('menu');
    }

    /**
     * {@inheritDoc}
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
