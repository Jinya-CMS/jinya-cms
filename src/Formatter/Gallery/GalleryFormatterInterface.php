<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 18.02.2018
 * Time: 15:42
 */

namespace Jinya\Formatter\Gallery;

use Jinya\Entity\Gallery;
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
     * Formats the slug
     *
     * @return GalleryFormatterInterface
     */
    public function slug(): self;

    /**
     * Formats the name
     *
     * @return GalleryFormatterInterface
     */
    public function name(): self;

    /**
     * Formats the description
     *
     * @return GalleryFormatterInterface
     */
    public function description(): self;

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
     * Formats the orientation
     *
     * @return GalleryFormatterInterface
     */
    public function orientation(): self;

    /**
     * Formats the artworks
     *
     * @return GalleryFormatterInterface
     */
    public function artworks(): self;

    /**
     * Formats the background
     *
     * @return GalleryFormatterInterface
     */
    public function background(): self;

    /**
     * Formats the labels
     *
     * @return GalleryFormatterInterface
     */
    public function labels(): self;

    /**
     * Formats the id
     *
     * @return GalleryFormatterInterface
     */
    public function id(): self;
}
