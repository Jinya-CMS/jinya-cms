<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 24.08.18
 * Time: 18:08
 */

namespace Jinya\Framework\Events\History;

use Jinya\Framework\Events\Common\CancellableEvent;

class HistoryRevertEvent extends CancellableEvent
{
    public const PRE_REVERT = 'HistoryPreRevert';

    public const POST_REVERT = 'HistoryPostRevert';

    /** @var string */
    private $class;

    /** @var int */
    private $id;

    /** @var string */
    private $field;

    /** @var string */
    private $timestamp;

    /** @var array */
    private $entry;

    /**
     * HistoryRevertEvent constructor.
     * @param string $class
     * @param int $id
     * @param string $field
     * @param string $timestamp
     * @param array $entry
     */
    public function __construct(string $class, int $id, string $field, string $timestamp, array $entry)
    {
        $this->class = $class;
        $this->id = $id;
        $this->field = $field;
        $this->timestamp = $timestamp;
        $this->entry = $entry;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @return string
     */
    public function getTimestamp(): string
    {
        return $this->timestamp;
    }
}
