<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 20.08.18
 * Time: 09:41
 */

namespace Jinya\Framework\Events\Artworks;

use Jinya\Entity\Artwork\Artwork;
use Jinya\Framework\Events\Common\CancellableEvent;

class ArtworkEvent extends CancellableEvent
{
    public const PRE_SAVE = 'ArtworkPreSave';

    public const POST_SAVE = 'ArtworkPostSave';

    public const PRE_GET = 'ArtworkPreGet';

    public const POST_GET = 'ArtworkPostGet';

    public const PRE_DELETE = 'ArtworkPreDelete';

    public const POST_DELETE = 'ArtworkPostDelete';

    /** @var Artwork */
    private $artwork;

    /** @var string */
    private $slug;

    /**
     * ArtworkEvent constructor.
     * @param Artwork $artwork
     * @param string $slug
     */
    public function __construct(?Artwork $artwork, ?string $slug)
    {
        $this->artwork = $artwork;
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
     * @return Artwork
     */
    public function getArtwork(): ?Artwork
    {
        return $this->artwork;
    }
}
