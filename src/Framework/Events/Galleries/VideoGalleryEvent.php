<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 20.08.18
 * Time: 09:41
 */

namespace Jinya\Framework\Events\Galleries;

use Jinya\Entity\Gallery\VideoGallery;
use Jinya\Framework\Events\Common\CancellableEvent;

class VideoGalleryEvent extends CancellableEvent
{
    public const PRE_SAVE = 'VideoGalleryPreSave';

    public const POST_SAVE = 'VideoGalleryPostSave';

    public const PRE_GET = 'VideoGalleryPreGet';

    public const POST_GET = 'VideoGalleryPostGet';

    public const PRE_DELETE = 'VideoGalleryPreDelete';

    public const POST_DELETE = 'VideoGalleryPostDelete';

    /** @var VideoGallery */
    private $videoGallery;

    /** @var string */
    private $slug;

    /**
     * VideoGalleryEvent constructor.
     * @param VideoGallery $videoGallery
     * @param string $slug
     */
    public function __construct(?VideoGallery $videoGallery, string $slug)
    {
        $this->videoGallery = $videoGallery;
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
     * @return VideoGallery
     */
    public function getVideoGallery(): ?VideoGallery
    {
        return $this->videoGallery;
    }
}
