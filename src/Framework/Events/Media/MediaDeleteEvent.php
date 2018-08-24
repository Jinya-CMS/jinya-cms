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

    /** @var string */
    private $type;

    /** @var string */
    private $filename;

    /**
     * MediaDeleteEvent constructor.
     * @param string $type
     * @param string $filename
     */
    public function __construct(string $type, string $filename)
    {
        $this->type = $type;
        $this->filename = $filename;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }
}
