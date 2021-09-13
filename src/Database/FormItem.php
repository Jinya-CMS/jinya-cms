<?php

namespace App\Database;

use App\Database\Strategies\JsonStrategy;
use Iterator;
use JetBrains\PhpStorm\Pure;
use Laminas\Hydrator\Strategy\BooleanStrategy;
use RuntimeException;

class FormItem extends Utils\RearrangableEntity implements Utils\FormattableEntityInterface
{
    public string $type = 'text';
    public array $options = [];
    public array $spamFilter = [];
    public string $label;
    public string $helpText = '';
    public int $formId;
    public bool $isFromAddress = false;
    public bool $isSubject = false;
    public bool $isRequired = false;
    public ?string $placeholder = '';

    /**
     * {@inheritDoc}
     */
    public static function findById(int $id): ?object
    {
        throw new RuntimeException('Not implemented');
    }

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
     * Gets the form item at the given position in the given form
     *
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     * @noinspection PhpIncompatibleReturnTypeInspection
     */
    public static function findByPosition(int $id, int $position): ?FormItem
    {
        $sql = 'SELECT id, form_id, type, options, spam_filter, label, help_text, position, is_from_address, is_subject, is_required, placeholder FROM form_item WHERE form_id = :id AND position = :position';

        $result = self::executeStatement(
            $sql,
            [
                'id' => $id,
                'position' => $position,
            ]
        );
        if (0 === count($result)) {
            return null;
        }

        return self::hydrateSingleResult(
            $result[0],
            new self(),
            [
                'spamFilter' => new JsonStrategy(),
                'options' => new JsonStrategy(),
            ]
        );
    }

    /**
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     *
     * @return Form|null
     */
    public function getForm(): ?Form
    {
        return Form::findById($this->formId);
    }

    #[Pure] public function format(): array
    {
        return [
            'type' => $this->type,
            'options' => $this->options,
            'spamFilter' => $this->spamFilter,
            'label' => $this->label,
            'placeholder' => $this->placeholder,
            'helpText' => $this->helpText,
            'position' => $this->position,
            'id' => $this->getIdAsInt(),
            'isRequired' => $this->isRequired,
            'isFromAddress' => $this->isFromAddress,
            'isSubject' => $this->isSubject,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function create(): void
    {
        $this->internalRearrange('form_item', 'form_id', $this->formId, $this->position);
        $this->internalCreate(
            'form_item',
            [
                'spamFilter' => new JsonStrategy(),
                'options' => new JsonStrategy(),
                'isRequired' => new BooleanStrategy(1, 0),
                'isFromAddress' => new BooleanStrategy(1, 0),
                'isSubject' => new BooleanStrategy(1, 0),
            ]
        );
        $this->resetOrder('form_item', 'form_id', $this->formId);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(): void
    {
        $this->internalDelete('form_item');
        $this->internalRearrange('form_item', 'form_id', $this->formId, -1);
        $this->resetOrder('form_item', 'form_id', $this->formId);
    }

    /**
     * {@inheritDoc}
     */
    public function update(): void
    {
        $this->internalUpdate(
            'form_item',
            [
                'spamFilter' => new JsonStrategy(),
                'options' => new JsonStrategy(),
                'isRequired' => new BooleanStrategy(1, 0),
                'isFromAddress' => new BooleanStrategy(1, 0),
                'isSubject' => new BooleanStrategy(1, 0),
            ]
        );
    }

    /**
     * {@inheritDoc}
     */
    public function move(int $newPosition): void
    {
        $this->internalRearrange('form_item', 'form_id', $this->formId, $newPosition);
        $this->position = $newPosition;
        $this->update();
        $this->resetOrder('form_item', 'form_id', $this->formId);
    }
}
