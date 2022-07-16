<?php

namespace App\Database;

use App\Database\Strategies\JsonStrategy;
use Iterator;
use JetBrains\PhpStorm\Pure;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;
use Laminas\Hydrator\Strategy\BooleanStrategy;
use RuntimeException;

/**
 * This class contains a form item, a form has several items
 */
class FormItem extends Utils\RearrangableEntity
{
    /** @var string The type of the form item, currently supported are select, checkbox and textarea. You can also specify any valid HTML5 form type */
    public string $type = 'text';
    /** @var array<string> The options of the form item, only needed when the type is select */
    public array $options = [];
    /** @var array<string> The spam filter contains a list of keywords, by which a submitted form is declared as spam. Messages marked as spam will not be send to the to address of the form */
    public array $spamFilter = [];
    /** @var string The label of the form item */
    public string $label;
    /** @var string The help text of the form item, it is theme dependent if and if yes, how they will be displayed */
    public string $helpText = '';
    /** @var int The ID of the form this form item belongs to */
    public int $formId;
    /** @var bool If true this form items value is considered the from address in the submitted message and the artist will reply to this */
    public bool $isFromAddress = false;
    /** @var bool If true this form items value will be used for the subject of the mail */
    public bool $isSubject = false;
    /** @var bool If true this form item is required and needs to be filled out */
    public bool $isRequired = false;
    /** @var string|null The placeholder text for the form element rendered, may be null */
    public ?string $placeholder = '';

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
     * Gets the form item at the given position in the given form
     *
     * @throws Exceptions\ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     * @throws NoResultException
     * @throws NoResultException
     */
    public static function findByPosition(int $id, int $position): ?FormItem
    {
        $sql = 'SELECT id, form_id, type, options, spam_filter, label, help_text, position, is_from_address, is_subject, is_required, placeholder FROM form_item WHERE form_id = :id AND position = :position';

        try {
            return self::getPdo()->fetchObject($sql, new self(),
                [
                    'id' => $id,
                    'position' => $position,
                ],
                [
                    'spamFilter' => new JsonStrategy(),
                    'options' => new JsonStrategy(),
                ]);
        } catch (InvalidQueryException$exception) {
            throw self::convertInvalidQueryExceptionToException($exception);
        }
    }

    /**
     * Gets the form of the current form item
     *
     * @return Form|null
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public function getForm(): ?Form
    {
        return Form::findById($this->formId);
    }

    /**
     * Not implemented
     */
    public static function findById(int $id): ?object
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * Formats the form item into an array
     *
     * @return array<string, array<string>|bool|int|string|null>
     */
    #[Pure]
    public function format(): array
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
     * Creates the current form item, also moves the position of the other form items according to the new position
     *
     * @return void
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
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
     * Deletes the current form item, the order of the remaining items will be reset
     *
     * @return void
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function delete(): void
    {
        $this->internalDelete('form_item');
        $this->internalRearrange('form_item', 'form_id', $this->formId, -1);
        $this->resetOrder('form_item', 'form_id', $this->formId);
    }

    /**
     * Moves the current form item to the new position. All other form items are rearranged accordingly
     *
     * @param int $newPosition
     * @return void
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function move(int $newPosition): void
    {
        $this->internalRearrange('form_item', 'form_id', $this->formId, $newPosition);
        $this->position = $newPosition;
        $this->update();
        $this->resetOrder('form_item', 'form_id', $this->formId);
    }

    /**
     * Updates the given form item
     *
     * @return void
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
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
}
