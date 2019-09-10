<?php

namespace Jinya\Formatter\Media;

use Jinya\Entity\Media\GalleryFilePosition;
use Jinya\Formatter\FormatterInterface;

interface GalleryFilePositionFormatterInterface extends FormatterInterface
{
    /**
     * Initializes the formatting
     *
     * @param GalleryFilePosition $filePosition
     * @return GalleryFilePositionFormatterInterface
     */
    public function init(GalleryFilePosition $filePosition): self;

    /**
     * Formats the type
     *
     * @return GalleryFilePositionFormatterInterface
     */
    public function file(): self;

    /**
     * Formats the name
     *
     * @return GalleryFilePositionFormatterInterface
     */
    public function gallery(): self;

    /**
     * Formats the folder
     *
     * @return GalleryFilePositionFormatterInterface
     */
    public function position(): self;

    /*
     *
     * @return GalleryFilePositionFormatterInterface
     */
    public function id(): self;
}
