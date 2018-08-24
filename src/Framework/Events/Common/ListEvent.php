<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 20.08.18
 * Time: 09:55
 */

namespace Jinya\Framework\Events\Common;

use Symfony\Component\EventDispatcher\Event;

class ListEvent extends Event
{
    public const ARTWORKS_POST_GET_ALL = 'ArtworksPostGetAll';

    public const ARTWORKS_PRE_GET_ALL = 'ArtworksPreGetAll';

    public const FORMS_POST_GET_ALL = 'FormsPostGetAll';

    public const FORMS_PRE_GET_ALL = 'FormsPreGetAll';

    public const ART_GALLERIES_POST_GET_ALL = 'ArtGalleriesPostGetAll';

    public const ART_GALLERIES_PRE_GET_ALL = 'ArtGalleriesPreGetAll';

    public const VIDEO_GALLERIES_POST_GET_ALL = 'VideoGalleriesPreGetAll';

    public const VIDEO_GALLERIES_PRE_GET_ALL = 'VideoGalleriesPostGetAll';

    public const MENU_POST_GET_ALL = 'MenuPreGetAll';

    public const MENU_PRE_GET_ALL = 'MenuPostGetAll';

    public const PAGE_POST_GET_ALL = 'PagePreGetAll';

    public const PAGE_PRE_GET_ALL = 'PagePostGetAll';

    /** @var int */
    private $offset;

    /** @var int */
    private $count;

    /** @var string */
    private $keyword;

    /** @var array */
    private $items;

    /**
     * ListEvent constructor.
     * @param int $offset
     * @param int $count
     * @param string $keyword
     * @param array $items
     */
    public function __construct(int $offset, int $count, string $keyword, array $items)
    {
        $this->offset = $offset;
        $this->count = $count;
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
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @return string
     */
    public function getKeyword(): string
    {
        return $this->keyword;
    }
}
