<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 20.08.18
 * Time: 09:41
 */

namespace Jinya\Framework\Events\Form;

use Jinya\Entity\Form\Message;
use Jinya\Framework\Events\Common\CancellableEvent;

class MessageEvent extends CancellableEvent
{
    public const PRE_SAVE = 'MessagePreSave';

    public const POST_SAVE = 'MessagePostSave';

    public const PRE_GET = 'MessagePreGet';

    public const POST_GET = 'MessagePostGet';

    public const PRE_DELETE = 'MessagePreDelete';

    public const POST_DELETE = 'MessagePostDelete';

    /** @var Message */
    private ?Message $message;

    /**
     * MessageEvent constructor.
     * @param Message $message
     */
    public function __construct(?Message $message)
    {
        $this->message = $message;
    }

    /**
     * @return Message
     */
    public function getMessage(): ?Message
    {
        return $this->message;
    }
}
