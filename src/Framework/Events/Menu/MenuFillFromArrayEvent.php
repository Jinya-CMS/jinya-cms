<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 24.08.18
 * Time: 21:54
 */

namespace Jinya\Framework\Events\Menu;

use Jinya\Framework\Events\Common\CancellableEvent;

class MenuFillFromArrayEvent extends CancellableEvent
{
    public const PRE_FILL_FROM_ARRAY = 'MenuPreFillFromArray';

    public const POST_FILL_FROM_ARRAY = 'MenuPostFillFromArray';

    private int $id;

    private array $data;

    /**
     * MenuFillFromArrayEvent constructor.
     */
    public function __construct(int $id, array $data)
    {
        $this->id = $id;
        $this->data = $data;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
