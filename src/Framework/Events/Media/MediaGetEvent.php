<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 24.08.18
 * Time: 19:08
 */

namespace Jinya\Framework\Events\Media;

use Symfony\Component\EventDispatcher\Event;

class MediaGetEvent extends Event
{
    public const PRE_GET = 'MediaPreGet';

    public const POST_GET = 'MediaPostGet';

    /** @var string */
    private $path;

    /** @var string|\SplFileInfo */
    private $result;

    /**
     * MediaGetEvent constructor.
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return \SplFileInfo|string
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param \SplFileInfo|string $result
     */
    public function setResult($result): void
    {
        $this->result = $result;
    }
}
