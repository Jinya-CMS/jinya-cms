<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 24.08.18
 * Time: 18:01
 */

namespace Jinya\Framework\Events\History;

use Jinya\Framework\Events\Common\CancellableEvent;

class HistoryEvent extends CancellableEvent
{
    public const PRE_GET = 'HistoryPreGet';

    public const POST_GET = 'HistoryPostGet';

    public const PRE_CLEAR = 'HistoryPreClear';

    public const POST_CLEAR = 'HistoryPostClear';

    private string $class;

    private int $id;

    private array $history;

    /**
     * HistoryEvent constructor.
     */
    public function __construct(string $class, int $id, array $history)
    {
        $this->class = $class;
        $this->id = $id;
        $this->history = $history;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getHistory(): array
    {
        return $this->history;
    }
}
