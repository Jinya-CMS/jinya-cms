<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 18.02.2018
 * Time: 16:11
 */

namespace Jinya\Formatter\Artwork;

use Jinya\Entity\Artwork\Artwork;
use Jinya\Formatter\FormatterInterface;
use Jinya\Formatter\User\UserFormatterInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ArtworkFormatter implements ArtworkFormatterInterface
{
    /** @var Artwork */
    private $artwork;

    /** @var array */
    private $formattedData;

    /** @var UserFormatterInterface */
    private $userFormatter;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /** @var ArtworkPositionFormatterInterface */
    private $artworkPositionFormatter;

    /** @var string */
    private $kernelProjectDir;

    /**
     * ArtworkFormatter constructor.
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
     * @param ArtworkPositionFormatterInterface $artworkPositionFormatter
     */
    public function setArtworkPositionFormatter(ArtworkPositionFormatterInterface $artworkPositionFormatter): void
    {
        $this->artworkPositionFormatter = $artworkPositionFormatter;
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
        $this->formattedData['picture'] = $this->urlGenerator->generate('api_artwork_picture_get', ['slug' => $this->artwork->getSlug()], UrlGeneratorInterface::ABSOLUTE_URL);

        return $this;
    }

    /**
     * Formats the labels
     *
     * @return ArtworkFormatterInterface
     */
    public function labels(): ArtworkFormatterInterface
    {
        $this->formattedData['labels'] = [];

        foreach ($this->artwork->getLabels() as $label) {
            $this->formattedData['labels'][] = ['name' => $label->getName()];
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
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function galleries(): ArtworkFormatterInterface
    {
        $this->formattedData['galleries'] = [];

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

    /**
     * Formats the id of the entity
     *
     * @return ArtworkFormatterInterface
     */
    public function id(): ArtworkFormatterInterface
    {
        $this->formattedData['id'] = $this->artwork->getId();

        return $this;
    }

    /**
     * Gets the dimensions of the artwork
     *
     * @return ArtworkFormatterInterface
     */
    public function dimensions(): ArtworkFormatterInterface
    {
        $imagePath = $this->kernelProjectDir . DIRECTORY_SEPARATOR . 'public' . $this->artwork->getPicture();
        if (file_exists($imagePath)) {
            $imageSize = getimagesize($imagePath);
            $this->formattedData['dimensions']['width'] = $imageSize[0];
            $this->formattedData['dimensions']['height'] = $imageSize[1];
        }

        return $this;
    }
}
