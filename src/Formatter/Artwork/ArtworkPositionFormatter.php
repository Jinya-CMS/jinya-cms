<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 18.02.2018
 * Time: 16:20
 */

namespace Jinya\Formatter\Artwork;


use Jinya\Entity\ArtworkPosition;
use Jinya\Formatter\Gallery\GalleryFormatterInterface;

class ArtworkPositionFormatter implements ArtworkPositionFormatterInterface
{
    /** @var array */
    private $formattedData;

    /** @var ArtworkPosition */
    private $artworkPosition;

    /** @var GalleryFormatterInterface */
    private $galleryFormatterInterface;

    /** @var ArtworkFormatterInterface */
    private $artworkFormatterInterface;

    /**
     * ArtworkPositionFormatter constructor.
     * @param GalleryFormatterInterface $galleryFormatterInterface
     * @param ArtworkFormatterInterface $artworkFormatterInterface
     */
    public function __construct(GalleryFormatterInterface $galleryFormatterInterface, ArtworkFormatterInterface $artworkFormatterInterface)
    {
        $this->galleryFormatterInterface = $galleryFormatterInterface;
        $this->artworkFormatterInterface = $artworkFormatterInterface;
    }

    /**
     * Initializes the format
     *
     * @param ArtworkPosition $artworkPosition
     * @return ArtworkPositionFormatterInterface
     */
    public function init(ArtworkPosition $artworkPosition): ArtworkPositionFormatterInterface
    {
        $this->formattedData = [];
        $this->artworkPosition = $artworkPosition;

        return $this;
    }

    /**
     * Formats the artwork
     *
     * @return ArtworkPositionFormatterInterface
     */
    public function artwork(): ArtworkPositionFormatterInterface
    {
        $this->formattedData['artwork'] = $this->artworkFormatterInterface
            ->init($this->artworkPosition->getArtwork())
            ->slug()
            ->name()
            ->description()
            ->format();

        return $this;
    }

    /**
     * Formats the gallery
     *
     * @return ArtworkPositionFormatterInterface
     */
    public function gallery(): ArtworkPositionFormatterInterface
    {
        $this->formattedData['gallery'] = $this->galleryFormatterInterface
            ->init($this->artworkPosition->getGallery())
            ->slug()
            ->name()
            ->description()
            ->format();

        return $this;
    }

    /**
     * Formats the position
     *
     * @return ArtworkPositionFormatterInterface
     */
    public function position(): ArtworkPositionFormatterInterface
    {
        $this->formattedData['id'] = $this->artworkPosition->getPosition();

        return $this;
    }

    /**
     * Formats the id
     *
     * @return ArtworkPositionFormatterInterface
     */
    public function id(): ArtworkPositionFormatterInterface
    {
        $this->formattedData['id'] = $this->artworkPosition->getId();

        return $this;
    }

    /**
     * Formats the content of the @see FormatterInterface into an array
     *
     * @return array
     */
    public function format(): array
    {
        return $this->formattedData;
    }
}