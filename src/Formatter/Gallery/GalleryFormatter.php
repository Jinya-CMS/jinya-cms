<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 18.02.2018
 * Time: 15:52
 */

namespace Jinya\Formatter\Gallery;

use Jinya\Entity\ArtworkPosition;
use Jinya\Entity\Gallery;
use Jinya\Formatter\Artwork\ArtworkPositionFormatterInterface;
use Jinya\Formatter\User\UserFormatterInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class GalleryFormatter implements GalleryFormatterInterface
{
    /** @var Gallery */
    private $gallery;

    /** @var array */
    private $formattedData;

    /** @var UserFormatterInterface */
    private $userFormatter;

    /** @var ArtworkPositionFormatterInterface */
    private $artworkPositionFormatter;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /**
     * GalleryFormatter constructor.
     * @param UserFormatterInterface $userFormatter
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(UserFormatterInterface $userFormatter, UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
        $this->userFormatter = $userFormatter;
    }

    /**
     * @param ArtworkPositionFormatterInterface $artworkPositionFormatter
     */
    public function setArtworkPositionFormatter(ArtworkPositionFormatterInterface $artworkPositionFormatter): void
    {
        $this->artworkPositionFormatter = $artworkPositionFormatter;
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

    /**
     * Initializes the formatting
     *
     * @param Gallery $gallery
     * @return GalleryFormatterInterface
     */
    public function init(Gallery $gallery): GalleryFormatterInterface
    {
        $this->gallery = $gallery;
        $this->formattedData = [];

        return $this;
    }

    /**
     * Formats the slug
     *
     * @return GalleryFormatterInterface
     */
    public function slug(): GalleryFormatterInterface
    {
        $this->formattedData['slug'] = $this->gallery->getSlug();

        return $this;
    }

    /**
     * Formats the name
     *
     * @return GalleryFormatterInterface
     */
    public function name(): GalleryFormatterInterface
    {
        $this->formattedData['name'] = $this->gallery->getName();

        return $this;
    }

    /**
     * Formats the description
     *
     * @return GalleryFormatterInterface
     */
    public function description(): GalleryFormatterInterface
    {
        $this->formattedData['description'] = $this->gallery->getDescription();

        return $this;
    }

    /**
     * Formats the created info
     *
     * @return GalleryFormatterInterface
     */
    public function created(): GalleryFormatterInterface
    {
        $this->formattedData['created']['by'] = $this->userFormatter
            ->init($this->gallery->getCreator())
            ->profile()
            ->format();
        $this->formattedData['created']['at'] = $this->gallery->getCreatedAt();

        return $this;
    }

    /**
     * Formats the updated info
     *
     * @return GalleryFormatterInterface
     */
    public function updated(): GalleryFormatterInterface
    {
        $this->formattedData['updated']['by'] = $this->userFormatter
            ->init($this->gallery->getUpdatedBy())
            ->profile()
            ->format();
        $this->formattedData['updated']['at'] = $this->gallery->getLastUpdatedAt();

        return $this;
    }

    /**
     * Formats the history
     *
     * @return GalleryFormatterInterface
     */
    public function history(): GalleryFormatterInterface
    {
        $this->formattedData['history'] = $this->gallery->getHistory();

        return $this;
    }

    /**
     * Formats the orientation
     *
     * @return GalleryFormatterInterface
     */
    public function orientation(): GalleryFormatterInterface
    {
        $this->formattedData['orientation'] = $this->gallery->getOrientation();

        return $this;
    }

    /**
     * Formats the artworks
     *
     * @return GalleryFormatterInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function artworks(): GalleryFormatterInterface
    {
        $this->formattedData['artworks'] = [];

        $artworkPositions = $this->gallery->getArtworks()->toArray();
        uasort($artworkPositions, function (ArtworkPosition $a, ArtworkPosition $b) {
            return $a->getPosition() > $b->getPosition();
        });

        $artworks = array_values($artworkPositions);

        /** @var ArtworkPosition $artworkPosition */
        foreach ($artworks as $artworkPosition) {
            $this->formattedData['artworks'][] = $this->artworkPositionFormatter
                ->init($artworkPosition)
                ->position()
                ->id()
                ->artwork()
                ->format();
        }

        return $this;
    }

    /**
     * Formats the background
     *
     * @return GalleryFormatterInterface
     */
    public function background(): GalleryFormatterInterface
    {
        $this->formattedData['background'] = $this->urlGenerator->generate('api_gallery_background_get', ['slug' => $this->gallery->getSlug()], UrlGeneratorInterface::ABSOLUTE_URL);

        return $this;
    }

    /**
     * Formats the labels
     *
     * @return GalleryFormatterInterface
     */
    public function labels(): GalleryFormatterInterface
    {
        $this->formattedData['labels'] = [];

        foreach ($this->gallery->getLabels() as $label) {
            $this->formattedData['labels'][] = ['name' => $label->getName()];
        }

        return $this;
    }

    /**
     * Formats the id
     *
     * @return GalleryFormatterInterface
     */
    public function id(): GalleryFormatterInterface
    {
        $this->formattedData['id'] = $this->gallery->getId();

        return $this;
    }
}