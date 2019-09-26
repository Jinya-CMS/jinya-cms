<?php

namespace Jinya\Formatter\Media;

use Jinya\Entity\Media\Gallery;
use Jinya\Entity\Media\GalleryFilePosition;
use Jinya\Formatter\User\UserFormatterInterface;

class GalleryFormatter implements GalleryFormatterInterface
{
    /** @var array */
    private $formattedData;

    /** @var GalleryFilePositionFormatterInterface */
    private $galleryFilePositionFormatter;

    /** @var UserFormatterInterface */
    private $userFormatter;

    /** @var Gallery */
    private $gallery;

    /**
     * GalleryFormatter constructor.
     * @param UserFormatterInterface $userFormatter
     */
    public function __construct(UserFormatterInterface $userFormatter)
    {
        $this->userFormatter = $userFormatter;
    }

    /**
     * @param GalleryFilePositionFormatterInterface $galleryFilePositionFormatter
     */
    public function setGalleryFilePositionFormatter(
        GalleryFilePositionFormatterInterface $galleryFilePositionFormatter
    ): void {
        $this->galleryFilePositionFormatter = $galleryFilePositionFormatter;
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
     * Formats the type
     *
     * @return GalleryFormatterInterface
     */
    public function description(): GalleryFormatterInterface
    {
        $this->formattedData['description'] = $this->gallery->getDescription();

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
     * Formats the folder
     *
     * @return GalleryFormatterInterface
     */
    public function slug(): GalleryFormatterInterface
    {
        $this->formattedData['slug'] = $this->gallery->getSlug();

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
     * Formats the gallery file positions
     *
     * @return GalleryFormatterInterface
     */
    public function files(): GalleryFormatterInterface
    {
        $files = $this->gallery->getFiles()->map(function (GalleryFilePosition $filePosition) {
            return $this->galleryFilePositionFormatter
                ->init($filePosition)
                ->id()
                ->position()
                ->file()
                ->gallery()
                ->format();
        })->toArray();

        uasort($files, static function (array $a, array $b) {
            return $a['position'] > $b['position'];
        });

        $this->formattedData['files'] = array_values($files);

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

    /**
     * Formats the type
     *
     * @return GalleryFormatterInterface
     */
    public function type(): GalleryFormatterInterface
    {
        $this->formattedData['type'] = $this->gallery->getType();

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
}
