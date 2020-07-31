<?php

namespace App\Database;

use Exception;
use Iterator;
use Laminas\Db\Sql\Predicate\PredicateSet;

class Menu extends Utils\LoadableEntity implements Utils\FormattableEntityInterface
{

    public string $name;
    public ?int $logo;

    /**
     * @inheritDoc
     * @return Menu
     */
    public static function findById(int $id)
    {
        return self::fetchSingleById('menu', $id, new self());
    }

    /**
     * @inheritDoc
     */
    public static function findByKeyword(string $keyword): Iterator
    {
        $sql = self::getSql();
        $select = $sql
            ->select()
            ->from('menu')
            ->where(['name LIKE :keyword'], PredicateSet::OP_OR);

        $result = self::executeStatement($sql->prepareStatementForSqlObject($select), ['keyword' => "%$keyword%"]);

        return self::hydrateMultipleResults($result, new self());
    }

    /**
     * @inheritDoc
     */
    public static function findAll(): Iterator
    {
        return self::fetchArray('menu', new self());
    }

    public function format(): array
    {
        $logo = $this->getLogo();
        $logoData = [];
        if ($logo) {
            $logoData['id'] = $logo->id;
            $logoData['name'] = $logo->name;
        }

        return [
            'name' => $this->name,
            'id' => $this->id,
            'logo' => $logoData,
        ];
    }

    /**
     * Gets the logo file
     *
     * @return File
     */
    public function getLogo(): ?File
    {
        if ($this->logo === null) {
            return null;
        }

        return File::findById($this->logo);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function create(): void
    {
        $this->internalCreate('menu');
    }

    /**
     * @inheritDoc
     */
    public function delete(): void
    {
        $this->internalDelete('menu');
    }

    /**
     * @inheritDoc
     */
    public function update(): void
    {
        $this->internalUpdate('menu');
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
            ->where(['menu_id = :id'])
            ->order('position ASC');

        $result = self::executeStatement($sql->prepareStatementForSqlObject($select), ['id' => $this->id]);

        return self::hydrateMultipleResults($result, new MenuItem());
    }
}