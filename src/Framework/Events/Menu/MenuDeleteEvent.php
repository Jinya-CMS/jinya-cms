<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 24.08.18
 * Time: 21:50
 */

namespace Jinya\Framework\Events\Menu;

use Jinya\Framework\Events\Common\CancellableEvent;

class MenuDeleteEvent extends CancellableEvent
{
    public const PRE_DELETE = 'MenuPreDelete';

    public const POST_DELETE = 'MenuPostDelete';

    private int $id;

    /**
     * MenuDeleteEvent constructor.
     */
    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
