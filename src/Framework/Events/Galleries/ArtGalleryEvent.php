<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 20.08.18
 * Time: 09:41
 */

namespace Jinya\Framework\Events\Galleries;

use Jinya\Entity\Gallery\ArtGallery;
use Jinya\Framework\Events\Common\CancellableEvent;

class ArtGalleryEvent extends CancellableEvent
{
    public const PRE_SAVE = 'ArtGalleryPreSave';

    public const POST_SAVE = 'ArtGalleryPostSave';

    public const PRE_GET = 'ArtGalleryPreGet';

    public const POST_GET = 'ArtGalleryPostGet';

    public const PRE_DELETE = 'ArtGalleryPreDelete';

    public const POST_DELETE = 'ArtGalleryPostDelete';

    /** @var ArtGallery */
    private $artGallery;

    /** @var string */
    private $slug;

    /**
     * ArtGalleryEvent constructor.
     * @param ArtGallery $artGallery
     * @param string $slug
     */
    public function __construct(?ArtGallery $artGallery, ?string $slug)
    {
        $this->artGallery = $artGallery;
        $this->slug = $slug;
    }

    /**
     * @return string
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @return ArtGallery
     */
    public function getArtGallery(): ?ArtGallery
    {
        return $this->artGallery;
    }
}
