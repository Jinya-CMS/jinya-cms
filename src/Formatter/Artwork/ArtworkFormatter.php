<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 18.02.2018
 * Time: 16:11
 */

namespace Jinya\Formatter\Artwork;


use Jinya\Entity\Artwork;
use Jinya\Formatter\Label\LabelFormatterInterface;
use Jinya\Formatter\User\UserFormatterInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ArtworkFormatter implements ArtworkFormatterInterface
{
    /** @var Artwork */
    private $artwork;

    /** @var array */
    private $formattedData;

    /** @var UserFormatterInterface */
    private $userFormatter;

    /** @var LabelFormatterInterface */
    private $labelFormatter;

    /** @var ArtworkPositionFormatterInterface */
    private $artworkPositionFormatter;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /**
     * ArtworkFormatter constructor.
     * @param UserFormatterInterface $userFormatter
     * @param LabelFormatterInterface $labelFormatter
     * @param ArtworkPositionFormatterInterface $artworkPositionFormatter
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(UserFormatterInterface $userFormatter, LabelFormatterInterface $labelFormatter, ArtworkPositionFormatterInterface $artworkPositionFormatter, UrlGeneratorInterface $urlGenerator)
    {
        $this->userFormatter = $userFormatter;
        $this->labelFormatter = $labelFormatter;
        $this->artworkPositionFormatter = $artworkPositionFormatter;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * Initializes the formatting
     *
     * @param Artwork $artwork
     * @return ArtworkFormatterInterface
     */
    public function init(Artwork $artwork): ArtworkFormatterInterface
    {
        $this->artwork = $artwork;
        $this->formattedData = [];

        return $this;
    }

    /**
     * Formats the slug
     *
     * @return ArtworkFormatterInterface
     */
    public function slug(): ArtworkFormatterInterface
    {
        $this->formattedData['slug'] = $this->artwork->getSlug();

        return $this;
    }

    /**
     * Formats the name
     *
     * @return ArtworkFormatterInterface
     */
    public function name(): ArtworkFormatterInterface
    {
        $this->formattedData['name'] = $this->artwork->getName();

        return $this;
    }

    /**
     * Formats the description
     *
     * @return ArtworkFormatterInterface
     */
    public function description(): ArtworkFormatterInterface
    {
        $this->formattedData['description'] = $this->artwork->getDescription();

        return $this;
    }

    /**
     * Formats the created info
     *
     * @return ArtworkFormatterInterface
     */
    public function created(): ArtworkFormatterInterface
    {
        $this->formattedData['created']['by'] = $this->userFormatter
            ->init($this->artwork->getCreator())
            ->profile()
            ->format();
        $this->formattedData['created']['at'] = $this->artwork->getCreatedAt();

        return $this;
    }

    /**
     * Formats the updated info
     *
     * @return ArtworkFormatterInterface
     */
    public function updated(): ArtworkFormatterInterface
    {
        $this->formattedData['updated']['by'] = $this->userFormatter
            ->init($this->artwork->getUpdatedBy())
            ->profile()
            ->format();
        $this->formattedData['updated']['at'] = $this->artwork->getLastUpdatedAt();

        return $this;
    }

    /**
     * Formats the history
     *
     * @return ArtworkFormatterInterface
     */
    public function history(): ArtworkFormatterInterface
    {
        $this->formattedData['history'] = $this->artwork->getHistory();

        return $this;
    }

    /**
     * Formats the picture
     *
     * @return ArtworkFormatterInterface
     */
    public function picture(): ArtworkFormatterInterface
    {
        $this->formattedData['picture'] = $this->urlGenerator->generate('api_artwork_picture_get', ['slug' => $this->artwork->getSlug()]);

        return $this;
    }

    /**
     * Formats the labels
     *
     * @return ArtworkFormatterInterface
     */
    public function labels(): ArtworkFormatterInterface
    {
        foreach ($this->artwork->getLabels() as $label) {
            $this->formattedData['labels'][] = $this->labelFormatter->init($label)
                ->name()
                ->format();
        }

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

    /**
     * Formats the galleries
     *
     * @return ArtworkFormatterInterface
     */
    public function galleries(): ArtworkFormatterInterface
    {
        foreach ($this->artwork->getPositions() as $position) {
            $this->formattedData['galleries'][] = $this->artworkPositionFormatter
                ->init($position)
                ->position()
                ->id()
                ->gallery()
                ->format();
        }

        return $this;
    }
}