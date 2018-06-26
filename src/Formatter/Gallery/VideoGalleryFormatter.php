<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 18.02.2018
 * Time: 15:52
 */

namespace Jinya\Formatter\Gallery;

use Jinya\Entity\Galleries\VideoGallery;
use Jinya\Entity\Galleries\VideoPosition;
use Jinya\Formatter\Video\VideoPositionFormatterInterface;
use Jinya\Formatter\User\UserFormatterInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class VideoGalleryFormatter implements VideoGalleryFormatterInterface
{
    /** @var VideoGallery */
    private $gallery;

    /** @var array */
    private $formattedData;

    /** @var UserFormatterInterface */
    private $userFormatter;

    /** @var VideoPositionFormatterInterface */
    private $videoPositionFormatter;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /**
     * GalleryFormatter constructor.
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param UserFormatterInterface $userFormatter
     */
    public function setUserFormatter(UserFormatterInterface $userFormatter): void
    {
        $this->userFormatter = $userFormatter;
    }

    /**
     * @param VideoPositionFormatterInterface $videoPositionFormatter
     */
    public function setVideoPositionFormatter(VideoPositionFormatterInterface $videoPositionFormatter): void
    {
        $this->videoPositionFormatter = $videoPositionFormatter;
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
     * @param VideoGallery $gallery
     * @return VideoGalleryFormatterInterface
     */
    public function init(VideoGallery $gallery): VideoGalleryFormatterInterface
    {
        $this->gallery = $gallery;
        $this->formattedData = [];

        return $this;
    }

    /**
     * Formats the slug
     *
     * @return VideoGalleryFormatterInterface
     */
    public function slug(): VideoGalleryFormatterInterface
    {
        $this->formattedData['slug'] = $this->gallery->getSlug();

        return $this;
    }

    /**
     * Formats the name
     *
     * @return VideoGalleryFormatterInterface
     */
    public function name(): VideoGalleryFormatterInterface
    {
        $this->formattedData['name'] = $this->gallery->getName();

        return $this;
    }

    /**
     * Formats the description
     *
     * @return VideoGalleryFormatterInterface
     */
    public function description(): VideoGalleryFormatterInterface
    {
        $this->formattedData['description'] = $this->gallery->getDescription();

        return $this;
    }

    /**
     * Formats the created info
     *
     * @return VideoGalleryFormatterInterface
     */
    public function created(): VideoGalleryFormatterInterface
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
     * @return VideoGalleryFormatterInterface
     */
    public function updated(): VideoGalleryFormatterInterface
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
     * @return VideoGalleryFormatterInterface
     */
    public function history(): VideoGalleryFormatterInterface
    {
        $this->formattedData['history'] = $this->gallery->getHistory();

        return $this;
    }

    /**
     * Formats the orientation
     *
     * @return VideoGalleryFormatterInterface
     */
    public function orientation(): VideoGalleryFormatterInterface
    {
        $this->formattedData['orientation'] = $this->gallery->getOrientation();

        return $this;
    }

    /**
     * Formats the videos
     *
     * @return VideoGalleryFormatterInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function videos(): VideoGalleryFormatterInterface
    {
        $this->formattedData['videos'] = [];

        $videoPositions = $this->gallery->getVideos()->toArray();
        uasort($videoPositions, function (VideoPosition $a, VideoPosition $b) {
            return $a->getPosition() > $b->getPosition();
        });

        $videos = array_values($videoPositions);

        /** @var VideoPosition $videoPosition */
        foreach ($videos as $videoPosition) {
            $this->formattedData['videos'][] = $this->videoPositionFormatter
                ->init($videoPosition)
                ->position()
                ->id()
                ->video()
                ->format();
        }

        return $this;
    }

    /**
     * Formats the background
     *
     * @return VideoGalleryFormatterInterface
     */
    public function background(): VideoGalleryFormatterInterface
    {
        $this->formattedData['background'] = $this->urlGenerator->generate('api_gallery_background_get', ['slug' => $this->gallery->getSlug()], UrlGeneratorInterface::ABSOLUTE_URL);

        return $this;
    }

    /**
     * Formats the id
     *
     * @return VideoGalleryFormatterInterface
     */
    public function id(): VideoGalleryFormatterInterface
    {
        $this->formattedData['id'] = $this->gallery->getId();

        return $this;
    }
}
