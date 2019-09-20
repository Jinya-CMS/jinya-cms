<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 18.02.2018
 * Time: 15:52
 */

namespace Jinya\Formatter\Gallery;

use Jinya\Entity\Gallery\VideoGallery;
use Jinya\Entity\Video\VideoPosition;
use Jinya\Formatter\User\UserFormatterInterface;
use Jinya\Formatter\Video\VideoPositionFormatterInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
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

    /** @var string */
    private $kernelProjectDir;

    /**
     * GalleryFormatter constructor.
     * @param UrlGeneratorInterface $urlGenerator
     * @param string $kernelProjectDir
     */
    public function __construct(UrlGeneratorInterface $urlGenerator, string $kernelProjectDir)
    {
        $this->urlGenerator = $urlGenerator;
        $this->kernelProjectDir = $kernelProjectDir;
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
     * Formats the content of the @return array
     * @see FormatterInterface into an array
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
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function videos(): VideoGalleryFormatterInterface
    {
        $this->formattedData['videos'] = [];

        $videoPositions = $this->gallery->getVideos()->toArray();
        uasort($videoPositions, static function (VideoPosition $a, VideoPosition $b) {
            return $a->getPosition() > $b->getPosition();
        });

        $videos = array_values($videoPositions);

        /** @var VideoPosition $videoPosition */
        foreach ($videos as $videoPosition) {
            $this->videoPositionFormatter
                ->init($videoPosition)
                ->position()
                ->id();

            if (null !== $videoPosition->getYoutubeVideo()) {
                $this->videoPositionFormatter
                    ->youtubeVideo();
            } else {
                $this->videoPositionFormatter
                    ->video();
            }

            $this->formattedData['videos'][] = $this->videoPositionFormatter->format();
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
        $this->formattedData['background'] = $this->urlGenerator->generate(
            'api_gallery_art_background_get',
            ['slug' => $this->gallery->getSlug()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

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

    /**
     * Formats the background dimensions
     *
     * @return VideoGalleryFormatterInterface
     */
    public function backgroundDimensions(): VideoGalleryFormatterInterface
    {
        $imagePath = $this->kernelProjectDir . DIRECTORY_SEPARATOR . 'public' . $this->gallery->getBackground();
        if (is_file($imagePath)) {
            $imageSize = getimagesize($imagePath);
            $this->formattedData['dimensions']['width'] = $imageSize[0];
            $this->formattedData['dimensions']['height'] = $imageSize[1];
        }

        return $this;
    }

    /**
     * Formats the masonry option
     *
     * @return VideoGalleryFormatterInterface
     */
    public function masonry(): VideoGalleryFormatterInterface
    {
        $this->formattedData['masonry'] = $this->gallery->isMasonry();

        return $this;
    }
}
