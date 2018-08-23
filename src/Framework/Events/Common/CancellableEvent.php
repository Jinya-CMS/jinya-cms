<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 20.08.18
 * Time: 19:22
 */

namespace Jinya\Framework\Events\Common;


use Symfony\Component\EventDispatcher\Event;

class CancellableEvent extends Event
{

    /** @var boolean */
    private $cancel;

    /**
     * @return bool
     */
    public function isCancel(): bool
    {
        return $this->cancel;
    }

    /**
     * @param bool $cancel
     */
    public function setCancel(bool $cancel): void
    {
        $this->cancel = $cancel;
    }
}