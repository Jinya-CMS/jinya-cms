<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 20.08.18
 * Time: 09:55
 */

namespace Jinya\Framework\Events\Common;

use Symfony\Contracts\EventDispatcher\Event;

class ListEvent extends Event
{
    public const ARTWORKS_POST_GET_ALL = 'ArtworksPostGetAll';

    public const ARTWORKS_PRE_GET_ALL = 'ArtworksPreGetAll';

    public const FORMS_POST_GET_ALL = 'FormsPostGetAll';

    public const FORMS_PRE_GET_ALL = 'FormsPreGetAll';

    public const MESSAGES_POST_GET_ALL = 'MessagesPostGetAll';

    public const MESSAGES_PRE_GET_ALL = 'MessagesPreGetAll';

    public const ART_GALLERIES_POST_GET_ALL = 'ArtGalleriesPostGetAll';

    public const ART_GALLERIES_PRE_GET_ALL = 'ArtGalleriesPreGetAll';

    public const VIDEO_GALLERIES_POST_GET_ALL = 'VideoGalleriesPreGetAll';

    public const VIDEO_GALLERIES_PRE_GET_ALL = 'VideoGalleriesPostGetAll';

    public const MENU_POST_GET_ALL = 'MenuPreGetAll';

    public const MENU_PRE_GET_ALL = 'MenuPostGetAll';

    public const PAGE_POST_GET_ALL = 'PagePreGetAll';

    public const PAGE_PRE_GET_ALL = 'PagePostGetAll';

    public const ALL_VIDEOS_PRE_GET_ALL = 'AllVideosPreGetAll';

    public const ALL_VIDEOS_POST_GET_ALL = 'AllVideosPostGetAll';

    public const YOUTUBE_VIDEOS_PRE_GET_ALL = 'YoutubeVideosPreGetAll';

    public const YOUTUBE_VIDEOS_POST_GET_ALL = 'YoutubeVideosPostGetAll';

    public const VIDEOS_PRE_GET_ALL = 'VideosPreGetAll';

    public const VIDEOS_POST_GET_ALL = 'VideosPostGetAll';

    public const SEGMENT_PAGE_POST_GET_ALL = 'SegmentPagePostGetAll';

    public const SEGMENT_PAGE_PRE_GET_ALL = 'SegmentPagePreGetAll';

    /** @var string */
    private string $keyword;

    /** @var array */
    private array $items;

    /**
     * ListEvent constructor.
     * @param string $keyword
     * @param array $items
     */
    public function __construct(string $keyword, array $items)
    {
        $this->keyword = $keyword;
        $this->items = $items;
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @return string
     */
    public function getKeyword(): string
    {
        return $this->keyword;
    }
}
