<?php

namespace Jinya\Formatter\Media;

use Jinya\Entity\Media\Gallery;
use Jinya\Entity\Media\GalleryFilePosition;
use Jinya\Formatter\User\UserFormatterInterface;

class GalleryFormatter implements GalleryFormatterInterface
{
    /** @var array */
    private array $formattedData;

    /** @var GalleryFilePositionFormatterInterface */
    private GalleryFilePositionFormatterInterface $galleryFilePositionFormatter;

    /** @var UserFormatterInterface */
    private UserFormatterInterface $userFormatter;

    /** @var Gallery */
    private Gallery $gallery;

    /**
     * GalleryFormatter constructor.
     */
    public function __construct(UserFormatterInterface $userFormatter)
    {
        $this->userFormatter = $userFormatter;
    }

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
     */
    public function init(Gallery $gallery): GalleryFormatterInterface
    {
        $this->gallery = $gallery;
        $this->formattedData = [];

        return $this;
    }

    /**
     * Formats the type
     */
    public function description(): GalleryFormatterInterface
    {
        $this->formattedData['description'] = $this->gallery->getDescription();

        return $this;
    }

    /**
     * Formats the name
     */
    public function name(): GalleryFormatterInterface
    {
        $this->formattedData['name'] = $this->gallery->getName();

        return $this;
    }

    /**
     * Formats the folder
     */
    public function slug(): GalleryFormatterInterface
    {
        $this->formattedData['slug'] = $this->gallery->getSlug();

        return $this;
    }

    /**
     * Formats the created info
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
     */
    public function history(): GalleryFormatterInterface
    {
        $this->formattedData['history'] = $this->gallery->getHistory();

        return $this;
    }

    /**
     * Formats the gallery file positions
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
     */
    public function id(): GalleryFormatterInterface
    {
        $this->formattedData['id'] = $this->gallery->getId();

        return $this;
    }

    /**
     * Formats the type
     */
    public function type(): GalleryFormatterInterface
    {
        $this->formattedData['type'] = $this->gallery->getType();

        return $this;
    }

    /**
     * Formats the orientation
     */
    public function orientation(): GalleryFormatterInterface
    {
        $this->formattedData['orientation'] = $this->gallery->getOrientation();

        return $this;
    }
}
