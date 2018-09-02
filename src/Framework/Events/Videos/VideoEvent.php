<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 20.08.18
 * Time: 09:41
 */

namespace Jinya\Framework\Events\Videos;

use Jinya\Entity\Video\Video;
use Jinya\Framework\Events\Common\CancellableEvent;

class VideoEvent extends CancellableEvent
{
    public const PRE_SAVE = 'VideoPreSave';

    public const POST_SAVE = 'VideoPostSave';

    public const PRE_GET = 'VideoPreGet';

    public const POST_GET = 'VideoPostGet';

    public const PRE_DELETE = 'VideoPreDelete';

    public const POST_DELETE = 'VideoPostDelete';

    /** @var Video */
    private $video;

    /** @var string */
    private $slug;

    /**
     * VideoEvent constructor.
     * @param Video $video
     * @param string $slug
     */
    public function __construct(?Video $video, string $slug)
    {
        $this->video = $video;
        $this->slug = $slug;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @return Video
     */
    public function getVideo(): ?Video
    {
        return $this->video;
    }
}
