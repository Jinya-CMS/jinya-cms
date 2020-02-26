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
    private string $class;

    /** @var int */
    private int $id;

    /** @var string */
    private string $field;

    /** @var string */
    private string $timestamp;

    /**
     * HistoryRevertEvent constructor.
     * @param string $class
     * @param int $id
     * @param string $field
     * @param string $timestamp
     */
    public function __construct(string $class, int $id, string $field, string $timestamp)
    {
        $this->class = $class;
        $this->id = $id;
        $this->field = $field;
        $this->timestamp = $timestamp;
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
