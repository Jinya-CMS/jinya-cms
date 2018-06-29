<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 18.02.2018
 * Time: 15:42
 */

namespace Jinya\Formatter\Gallery;

use Jinya\Entity\Galleries\VideoGallery;
use Jinya\Formatter\FormatterInterface;

interface VideoGalleryFormatterInterface extends FormatterInterface
{
    /**
     * Initializes the formatting
     *
     * @param VideoGallery $gallery
     * @return VideoGalleryFormatterInterface
     */
    public function init(VideoGallery $gallery): self;

    /**
     * Formats the slug
     *
     * @return VideoGalleryFormatterInterface
     */
    public function slug(): self;

    /**
     * Formats the name
     *
     * @return VideoGalleryFormatterInterface
     */
    public function name(): self;

    /**
     * Formats the description
     *
     * @return VideoGalleryFormatterInterface
     */
    public function description(): self;

    /**
     * Formats the created info
     *
     * @return VideoGalleryFormatterInterface
     */
    public function created(): self;

    /**
     * Formats the updated info
     *
     * @return VideoGalleryFormatterInterface
     */
    public function updated(): self;

    /**
     * Formats the history
     *
     * @return VideoGalleryFormatterInterface
     */
    public function history(): self;

    /**
     * Formats the orientation
     *
     * @return VideoGalleryFormatterInterface
     */
    public function orientation(): self;

    /**
     * Formats the videos
     *
     * @return VideoGalleryFormatterInterface
     */
    public function videos(): self;

    /**
     * Formats the background
     *
     * @return VideoGalleryFormatterInterface
     */
    public function background(): self;

    /**
     * Formats the id
     *
     * @return VideoGalleryFormatterInterface
     */
    public function id(): self;
}
