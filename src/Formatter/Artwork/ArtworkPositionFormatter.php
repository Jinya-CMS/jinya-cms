<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 18.02.2018
 * Time: 16:20
 */

namespace Jinya\Formatter\Artwork;

use Jinya\Entity\Artwork\ArtworkPosition;
use Jinya\Formatter\FormatterInterface;
use Jinya\Formatter\Gallery\ArtGalleryFormatterInterface;

class ArtworkPositionFormatter implements ArtworkPositionFormatterInterface
{
    /** @var array */
    private $formattedData;

    /** @var ArtworkPosition */
    private $artworkPosition;

    /** @var ArtGalleryFormatterInterface */
    private $galleryFormatterInterface;

    /** @var ArtworkFormatterInterface */
    private $artworkFormatterInterface;

    /**
     * @param ArtGalleryFormatterInterface $galleryFormatterInterface
     */
    public function setGalleryFormatterInterface(ArtGalleryFormatterInterface $galleryFormatterInterface): void
    {
        $this->galleryFormatterInterface = $galleryFormatterInterface;
    }

    /**
     * @param ArtworkFormatterInterface $artworkFormatterInterface
     */
    public function setArtworkFormatterInterface(ArtworkFormatterInterface $artworkFormatterInterface): void
    {
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
            ->picture()
            ->description()
            ->dimensions()
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
     * Formats the content of the @return array
     * @see FormatterInterface into an array
     */
    public function format(): array
    {
        return $this->formattedData;
    }
}
