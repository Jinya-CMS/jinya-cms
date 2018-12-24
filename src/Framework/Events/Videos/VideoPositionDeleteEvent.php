<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 20.08.18
 * Time: 19:12
 */

namespace Jinya\Framework\Events\Videos;

use Jinya\Entity\Gallery\VideoGallery;
use Jinya\Framework\Events\Common\CancellableEvent;

class VideoPositionDeleteEvent extends CancellableEvent
{
    public const PRE_DELETE = 'VideoPositionPreDelete';

    public const POST_DELETE = 'VideoPositionPostDelete';

    /** @var VideoGallery */
    private $gallery;

    /** @var int */
    private $id;

    /**
     * VideoPositionDeleteEvent constructor.
     * @param VideoGallery $gallery
     * @param int $id
     */
    public function __construct(VideoGallery $gallery, int $id)
    {
        $this->gallery = $gallery;
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return VideoGallery
     */
    public function getGallery(): ?VideoGallery
    {
        return $this->gallery;
    }
}
