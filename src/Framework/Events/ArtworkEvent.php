<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 20.08.18
 * Time: 09:41
 */

namespace Jinya\Framework\Events;

use Jinya\Entity\Artwork\Artwork;
use Symfony\Component\EventDispatcher\Event;

class ArtworkEvent extends Event
{
    public static const PRE_SAVE = 'ArtworkPreSave';

    public static const POST_SAVE = 'ArtworkPostSave';

    public static const PRE_GET = 'ArtworkPreGet';

    public static const POST_GET = 'ArtworkPostGet';

    public static const PRE_DELETE = 'ArtworkPreDelete';

    public static const POST_DELETE = 'ArtworkPostDelete';

    /** @var Artwork */
    private $artwork;

    /** @var string */
    private $slug;

    /** @var bool */
    private $cancel;

    /**
     * ArtworkEvent constructor.
     * @param Artwork $artwork
     * @param string $slug
     */
    public function __construct(Artwork $artwork, string $slug)
    {
        $this->artwork = $artwork;
        $this->slug = $slug;
        $this->cancel = false;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @return Artwork
     */
    public function getArtwork(): Artwork
    {
        return $this->artwork;
    }

    /**
     * @return bool
     */
    public function isCancel(): bool
    {
        return $this->cancel;
    }

    /**
     * @param bool $cancel
     */
    public function setCancel(bool $cancel): void
    {
        $this->cancel = $cancel;
    }
}
