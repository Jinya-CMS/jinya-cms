<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 24.08.18
 * Time: 19:08
 */

namespace Jinya\Framework\Events\Media;

use SplFileInfo;
use Symfony\Contracts\EventDispatcher\Event;

class MediaGetEvent extends Event
{
    public const PRE_GET = 'MediaPreGet';

    public const POST_GET = 'MediaPostGet';

    /** @var string */
    private string $path;

    /** @var string|SplFileInfo */
    private $result;

    /**
     * MediaGetEvent constructor.
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return SplFileInfo|string
     */
    public function getResult()
    {
        return $this->result;
    }

    public function setResult(SplFileInfo $result): void
    {
        $this->result = $result;
    }
}
