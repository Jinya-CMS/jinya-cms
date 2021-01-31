<?php

namespace App\Database;

use App\OpenApiGeneration\Attributes\OpenApiField;
use App\OpenApiGeneration\Attributes\OpenApiModel;
use Exception;
use Iterator;

#[OpenApiModel('A menu controls the navigation of the Jinya instance')]
class Menu extends Utils\LoadableEntity implements Utils\FormattableEntityInterface
{
    #[OpenApiField(required: true)]
    public string $name;
    #[OpenApiField(required: false, defaultValue: null, structure: [
        'name' => [
            'type' => 'string',
        ],
        'id' => [
            'type' => 'integer',
        ],
    ])]
    public ?int $logo;

    /**
     * {@inheritDoc}
     * @return Menu
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

        $result = self::executeStatement($sql, ['keyword' => "%$keyword%"]);

        return self::hydrateMultipleResults($result, new self());
    }

    /**
     * {@inheritDoc}
     */
    public static function findAll(): Iterator
    {
        return self::fetchArray('menu', new self());
    }

    /**
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
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
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    public function getLogo(): ?File
    {
        if (null === $this->logo) {
            return null;
        }

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
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    public function getItems(): Iterator
    {
        $sql = 'SELECT id, menu_id, parent_id, title, highlighted, position, artist_id, page_id, form_id, gallery_id, segment_page_id, route FROM menu_item WHERE menu_id = :id ORDER BY position';

        $result = self::executeStatement($sql, ['id' => $this->id]);

        return self::hydrateMultipleResults($result, new MenuItem());
    }
}
