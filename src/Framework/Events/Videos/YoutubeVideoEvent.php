<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 20.08.18
 * Time: 09:41
 */

namespace Jinya\Framework\Events\Videos;

use Jinya\Entity\Video\YoutubeVideo;
use Jinya\Framework\Events\Common\CancellableEvent;

class YoutubeVideoEvent extends CancellableEvent
{
    public const PRE_SAVE = 'YoutubeVideoPreSave';

    public const POST_SAVE = 'YoutubeVideoPostSave';

    public const PRE_GET = 'YoutubeVideoPreGet';

    public const POST_GET = 'YoutubeVideoPostGet';

    public const PRE_DELETE = 'YoutubeVideoPreDelete';

    public const POST_DELETE = 'YoutubeVideoPostDelete';

    /** @var YoutubeVideo */
    private $youtubeVideo;

    /** @var string */
    private $slug;

    /**
     * YoutubeVideoEvent constructor.
     * @param YoutubeVideo $youtubeVideo
     * @param string $slug
     */
    public function __construct(?YoutubeVideo $youtubeVideo, string $slug)
    {
        $this->youtubeVideo = $youtubeVideo;
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
     * @return YoutubeVideo
     */
    public function getYoutubeVideo(): ?YoutubeVideo
    {
        return $this->youtubeVideo;
    }
}
