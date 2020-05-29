<?php

namespace App\Database;

use Exception;
use Iterator;
use Laminas\Db\Sql\Predicate\PredicateSet;
use Laminas\Hydrator\Strategy\SerializableStrategy;
use Laminas\Serializer\Adapter\Json;
use RuntimeException;

class FormItem extends Utils\RearrangableEntity implements Utils\FormattableEntityInterface
{
    public string $type;
    public array $options;
    public array $spamFilter;
    public string $label;
    public string $helpText;
    public int $position;
    public int $formId;

    /**
     * @inheritDoc
     */
    public static function findById(int $id)
    {
        return self::fetchSingleById($id, $id, new self(), [
            'spamFilter' => new SerializableStrategy(new Json()),
            'options' => new SerializableStrategy(new Json()),
        ]);
    }

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
     * Gets the form the form at the given position
     *
     * @param int $id
     * @param int $position
     * @return FormItem
     */
    public static function findByPosition(int $id, int $position): FormItem
    {
        $sql = self::getSql();
        $select = $sql
            ->select()
            ->from('form_item')
            ->where(['id = :id', 'position = :position'], PredicateSet::OP_AND);

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return self::hydrateSingleResult($sql->prepareStatementForSqlObject($select)->execute([
            'id' => $id,
            'position' => $position
        ]), new self(), [
            'spamFilter' => new SerializableStrategy(new Json()),
            'options' => new SerializableStrategy(new Json()),
        ]);
    }

    /**
     * @return Form
     */
    public function getForm(): Form
    {
        return Form::findById($this->formId);
    }

    /**
     * @return array
     */
    public function format(): array
    {
        return [
            'type' => $this->type,
            'options' => $this->options,
            'spamFilter' => $this->spamFilter,
            'label' => $this->label,
            'helpText' => $this->helpText,
            'position' => $this->position,
            'id' => $this->id,
        ];
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function create(): void
    {
        $this->internalCreate('form_item', [
            'spamFilter' => new SerializableStrategy(new Json()),
            'options' => new SerializableStrategy(new Json()),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function delete(): void
    {
        $this->internalDelete('form_item');
    }

    /**
     * @inheritDoc
     */
    public function update(): void
    {
        $this->internalUpdate('form_item', [
            'spamFilter' => new SerializableStrategy(new Json()),
            'options' => new SerializableStrategy(new Json()),
        ]);
    }
    /**
     * Moves the given position
     *
     * @param int $newPosition
     * @throws Exceptions\UniqueFailedException
     */
    public function move(int $newPosition): void
    {
        $this->internalRearrange('form_item', 'form_id', $this->formId, $newPosition);
        $this->position = $newPosition;
        $this->update();
    }
}