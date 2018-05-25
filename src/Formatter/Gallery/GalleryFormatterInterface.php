<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 18.02.2018
 * Time: 15:42.
 */

namespace Jinya\Formatter\Gallery;

use Jinya\Entity\Gallery;
use Jinya\Formatter\FormatterInterface;

interface GalleryFormatterInterface extends FormatterInterface
{
    /**
     * Initializes the formatting.
     *
     * @param Gallery $gallery
     *
     * @return GalleryFormatterInterface
     */
    public function init(Gallery $gallery): GalleryFormatterInterface;

    /**
     * Formats the slug.
     *
     * @return GalleryFormatterInterface
     */
    public function slug(): GalleryFormatterInterface;

    /**
     * Formats the name.
     *
     * @return GalleryFormatterInterface
     */
    public function name(): GalleryFormatterInterface;

    /**
     * Formats the description.
     *
     * @return GalleryFormatterInterface
     */
    public function description(): GalleryFormatterInterface;

    /**
     * Formats the created info.
     *
     * @return GalleryFormatterInterface
     */
    public function created(): GalleryFormatterInterface;

    /**
     * Formats the updated info.
     *
     * @return GalleryFormatterInterface
     */
    public function updated(): GalleryFormatterInterface;

    /**
     * Formats the history.
     *
     * @return GalleryFormatterInterface
     */
    public function history(): GalleryFormatterInterface;

    /**
     * Formats the orientation.
     *
     * @return GalleryFormatterInterface
     */
    public function orientation(): GalleryFormatterInterface;

    /**
     * Formats the artworks.
     *
     * @return GalleryFormatterInterface
     */
    public function artworks(): GalleryFormatterInterface;

    /**
     * Formats the background.
     *
     * @return GalleryFormatterInterface
     */
    public function background(): GalleryFormatterInterface;

    /**
     * Formats the labels.
     *
     * @return GalleryFormatterInterface
     */
    public function labels(): GalleryFormatterInterface;

    /**
     * Formats the id.
     *
     * @return GalleryFormatterInterface
     */
    public function id(): GalleryFormatterInterface;
}
