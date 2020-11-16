<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 24.08.18
 * Time: 18:55
 */

namespace Jinya\Framework\Events\Media;

use Jinya\Framework\Events\Common\CancellableEvent;

class MediaDeleteEvent extends CancellableEvent
{
    public const PRE_DELETE = 'MediaPreDelete';

    public const POST_DELETE = 'MediaPostDelete';

    private string $type;

    private string $filename;

    /**
     * MediaDeleteEvent constructor.
     */
    public function __construct(string $type, string $filename)
    {
        $this->type = $type;
        $this->filename = $filename;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }
}
