<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 26.08.18
 * Time: 16:02
 */

namespace Jinya\Framework\Events\Videos;

use Jinya\Entity\Video\Video;
use Jinya\Framework\Events\Common\CancellableEvent;

class VideoUploadStartUploadEvent extends CancellableEvent
{
    public const PRE_START_UPLOAD = 'VideoUploadPreStartUpload';

    public const POST_START_UPLOAD = 'VideoUploadPostStartUpload';

    /** @var Video */
    private $video;

    /**
     * VideoUploadStartUploadEvent constructor.
     * @param Video $video
     */
    public function __construct(Video $video)
    {
        $this->video = $video;
    }

    /**
     * @return Video
     */
    public function getVideo(): Video
    {
        return $this->video;
    }
}
