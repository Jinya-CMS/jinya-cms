<?php

namespace Jinya\Formatter\Media;

use Jinya\Entity\Media\GalleryFilePosition;

class GalleryFilePositionFormatter implements GalleryFilePositionFormatterInterface
{
    /** @var GalleryFilePosition */
    private GalleryFilePosition $position;

    /** @var array */
    private array $formattedData;

    /** @var FileFormatterInterface */
    private FileFormatterInterface $fileFormatter;

    /** @var GalleryFormatterInterface */
    private GalleryFormatterInterface $galleryFormatter;

    public function setFileFormatter(FileFormatterInterface $fileFormatter): void
    {
        $this->fileFormatter = $fileFormatter;
    }

    public function setGalleryFormatter(GalleryFormatterInterface $galleryFormatter): void
    {
        $this->galleryFormatter = $galleryFormatter;
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
    public function init(GalleryFilePosition $filePosition): GalleryFilePositionFormatterInterface
    {
        $this->position = $filePosition;
        $this->formattedData = [];

        return $this;
    }

    /**
     * Formats the type
     */
    public function file(): GalleryFilePositionFormatterInterface
    {
        $this->formattedData['file'] = $this->fileFormatter->init($this->position->getFile())
            ->tags()
            ->path()
            ->id()
            ->name()
            ->type()
            ->format();

        return $this;
    }

    /**
     * Formats the name
     */
    public function gallery(): GalleryFilePositionFormatterInterface
    {
        $this->formattedData['gallery'] = $this->galleryFormatter->init($this->position->getGallery())
            ->id()
            ->name()
            ->slug()
            ->description()
            ->format();

        return $this;
    }

    /**
     * Formats the folder
     */
    public function position(): GalleryFilePositionFormatterInterface
    {
        $this->formattedData['position'] = $this->position->getPosition();

        return $this;
    }

    public function id(): GalleryFilePositionFormatterInterface
    {
        $this->formattedData['id'] = $this->position->getId();

        return $this;
    }
}
