<?php

namespace Jinya\Formatter\Media;

use Jinya\Entity\Media\Gallery;
use Jinya\Formatter\FormatterInterface;

interface GalleryFormatterInterface extends FormatterInterface
{
    /**
     * Initializes the formatting
     *
     * @param Gallery $gallery
     * @return GalleryFormatterInterface
     */
    public function init(Gallery $gallery): self;

    /**
     * Formats the type
     *
     * @return GalleryFormatterInterface
     */
    public function description(): self;

    /**
     * Formats the name
     *
     * @return GalleryFormatterInterface
     */
    public function name(): self;

    /**
     * Formats the folder
     *
     * @return GalleryFormatterInterface
     */
    public function slug(): self;

    /**
     * Formats the created info
     *
     * @return GalleryFormatterInterface
     */
    public function created(): self;

    /**
     * Formats the updated info
     *
     * @return GalleryFormatterInterface
     */
    public function updated(): self;

    /**
     * Formats the history
     *
     * @return GalleryFormatterInterface
     */
    public function history(): self;

    /**
     * Formats the gallery file positions
     *
     * @return GalleryFormatterInterface
     */
    public function files(): self;

    /**
     * Formats the id
     *
     * @return GalleryFormatterInterface
     */
    public function id(): self;
}
