<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 20.08.18
 * Time: 19:22
 */

namespace Jinya\Framework\Events\Common;

use Symfony\Contracts\EventDispatcher\Event;

class CancellableEvent extends Event
{
    private bool $cancel = false;

    public function isCancel(): bool
    {
        return $this->cancel;
    }

    public function setCancel(bool $cancel): void
    {
        if ($cancel) {
            $this->stopPropagation();
        }

        $this->cancel = $cancel;
    }
}
