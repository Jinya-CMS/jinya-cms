<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 20.08.18
 * Time: 09:51
 */

namespace Jinya\Framework\Events\Common;

use Symfony\Contracts\EventDispatcher\Event;

class CountEvent extends Event
{
    public const ARTWORKS_PRE_COUNT = 'ArtworksPreCount';

    public const ARTWORKS_POST_COUNT = 'ArtworksPostCount';

    public const ART_GALLERIES_PRE_COUNT = 'ArtGalleriesPreCount';

    public const ART_GALLERIES_POST_COUNT = 'ArtGalleriesPostCount';

    public const FORMS_PRE_COUNT = 'FormsPreCount';

    public const FORMS_POST_COUNT = 'FormsPostCount';

    public const MESSAGES_PRE_COUNT = 'MessagesPreCount';

    public const MESSAGES_POST_COUNT = 'MessagesPostCount';

    public const VIDEO_GALLERIES_PRE_COUNT = 'VideoGalleriesPreCount';

    public const VIDEO_GALLERIES_POST_COUNT = 'VideoGalleriesPostCount';

    public const PAGES_POST_COUNT = 'PagesPreCount';

    public const PAGES_PRE_COUNT = 'PagesPostCount';

    public const ALL_VIDEOS_PRE_COUNT = 'AllVideosPreCount';

    public const ALL_VIDEOS_POST_COUNT = 'AllVideosPostCount';

    public const YOUTUBE_VIDEOS_PRE_COUNT = 'YoutubeVideosPreCount';

    public const YOUTUBE_VIDEOS_POST_COUNT = 'YoutubeVideosPostCount';

    public const VIDEOS_PRE_COUNT = 'VideosPreCount';

    public const VIDEOS_POST_COUNT = 'VideosPostCount';

    public const SEGMENT_PAGES_PRE_COUNT = 'SegmentPagesPreCount';

    public const SEGMENT_PAGES_POST_COUNT = 'SegmentPagesPostCount';

    public const SEGMENTS_PRE_COUNT = 'SegmentPreCount';

    public const SEGMENTS_POST_COUNT = 'SegmentPostCount';

    /** @var string */
    private string $keyword;

    /** @var int */
    private int $count;

    /**
     * CountEvent constructor.
     */
    public function __construct(string $keyword, int $count)
    {
        $this->keyword = $keyword;
        $this->count = $count;
    }

    public function getKeyword(): string
    {
        return $this->keyword;
    }

    public function getCount(): int
    {
        return $this->count;
    }
}
