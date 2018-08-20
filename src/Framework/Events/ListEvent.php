<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 20.08.18
 * Time: 09:55
 */

namespace Jinya\Framework\Events;

use Symfony\Component\EventDispatcher\Event;

class ListEvent extends Event
{
    const ARTWORKS_POST_GET_ALL = 'ArtworksPostGetAll';

    const ARTWORKS_PRE_GET_ALL = 'ArtworksPreGetAll';

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
