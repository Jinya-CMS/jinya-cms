<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 18.02.2018
 * Time: 15:42
 */

namespace Jinya\Formatter\Gallery;

use Jinya\Entity\Gallery\ArtGallery;
use Jinya\Formatter\FormatterInterface;

interface ArtGalleryFormatterInterface extends FormatterInterface
{
    /**
     * Initializes the formatting
     *
     * @param ArtGallery $gallery
     * @return ArtGalleryFormatterInterface
     */
    public function init(ArtGallery $gallery): self;

    /**
     * Formats the slug
     *
     * @return ArtGalleryFormatterInterface
     */
    public function slug(): self;

    /**
     * Formats the name
     *
     * @return ArtGalleryFormatterInterface
     */
    public function name(): self;

    /**
     * Formats the description
     *
     * @return ArtGalleryFormatterInterface
     */
    public function description(): self;

    /**
     * Formats the created info
     *
     * @return ArtGalleryFormatterInterface
     */
    public function created(): self;

    /**
     * Formats the updated info
     *
     * @return ArtGalleryFormatterInterface
     */
    public function updated(): self;

    /**
     * Formats the history
     *
     * @return ArtGalleryFormatterInterface
     */
    public function history(): self;

    /**
     * Formats the orientation
     *
     * @return ArtGalleryFormatterInterface
     */
    public function orientation(): self;

    /**
     * Formats the artworks
     *
     * @return ArtGalleryFormatterInterface
     */
    public function artworks(): self;

    /**
     * Formats the background
     *
     * @return ArtGalleryFormatterInterface
     */
    public function background(): self;

    /**
     * Formats the labels
     *
     * @return ArtGalleryFormatterInterface
     */
    public function labels(): self;

    /**
     * Formats the id
     *
     * @return ArtGalleryFormatterInterface
     */
    public function id(): self;

    /**
     * Formats the background dimensions
     *
     * @return ArtGalleryFormatterInterface
     */
    public function backgroundDimensions(): self;

    /**
     * Formats the masonry property
     *
     * @return ArtGalleryFormatterInterface
     */
    public function masonry(): self;
}
