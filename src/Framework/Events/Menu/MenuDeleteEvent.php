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

    /** @var int */
    private $id;

    /**
     * MenuDeleteEvent constructor.
     * @param int $id
     */
    public function __construct(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
}