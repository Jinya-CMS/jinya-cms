<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 20.08.18
 * Time: 09:51
 */

namespace Jinya\Framework\Events\Common;

use Symfony\Component\EventDispatcher\Event;

class CountEvent extends Event
{
    public const ARTWORKS_PRE_COUNT = 'ArtworksPreCount';

    public const ARTWORKS_POST_COUNT = 'ArtworksPostCount';

    public const ART_GALLERIES_PRE_COUNT = 'ArtGalleriesPreCount';

    public const ART_GALLERIES_POST_COUNT = 'ArtGalleriesPostCount';

    public const FORMS_PRE_COUNT = 'FormsPreCount';

    public const FORMS_POST_COUNT = 'FormsPostCount';

    public const VIDEO_GALLERIES_PRE_COUNT = 'VideoGalleriesPreCount';

    public const VIDEO_GALLERIES_POST_COUNT = 'VideoGalleriesPostCount';

    public const PAGES_POST_COUNT = 'PagesPreCount';

    public const PAGES_PRE_COUNT = 'PagesPostCount';

    /** @var string */
    private $keyword;

    /** @var int */
    private $count;

    /**
     * CountEvent constructor.
     * @param string $keyword
     * @param int $count
     */
    public function __construct(string $keyword, int $count)
    {
        $this->keyword = $keyword;
        $this->count = $count;
    }

    /**
     * @return string
     */
    public function getKeyword(): string
    {
        return $this->keyword;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }
}
