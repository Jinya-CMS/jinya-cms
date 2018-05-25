<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 18.02.2018
 * Time: 15:46.
 */

namespace Jinya\Formatter\Artwork;

use Jinya\Entity\Artwork;
use Jinya\Formatter\FormatterInterface;

interface ArtworkFormatterInterface extends FormatterInterface
{
    /**
     * Initializes the formatting.
     *
     * @param Artwork $artwork
     *
     * @return ArtworkFormatterInterface
     */
    public function init(Artwork $artwork): ArtworkFormatterInterface;

    /**
     * Formats the slug.
     *
     * @return ArtworkFormatterInterface
     */
    public function slug(): ArtworkFormatterInterface;

    /**
     * Formats the name.
     *
     * @return ArtworkFormatterInterface
     */
    public function name(): ArtworkFormatterInterface;

    /**
     * Formats the description.
     *
     * @return ArtworkFormatterInterface
     */
    public function description(): ArtworkFormatterInterface;

    /**
     * Formats the created info.
     *
     * @return ArtworkFormatterInterface
     */
    public function created(): ArtworkFormatterInterface;

    /**
     * Formats the updated info.
     *
     * @return ArtworkFormatterInterface
     */
    public function updated(): ArtworkFormatterInterface;

    /**
     * Formats the history.
     *
     * @return ArtworkFormatterInterface
     */
    public function history(): ArtworkFormatterInterface;

    /**
     * Formats the picture.
     *
     * @return ArtworkFormatterInterface
     */
    public function picture(): ArtworkFormatterInterface;

    /**
     * Formats the labels.
     *
     * @return ArtworkFormatterInterface
     */
    public function labels(): ArtworkFormatterInterface;

    /**
     * Formats the galleries.
     *
     * @return ArtworkFormatterInterface
     */
    public function galleries(): ArtworkFormatterInterface;

    /**
     * Formats the id.
     *
     * @return ArtworkFormatterInterface
     */
    public function id(): ArtworkFormatterInterface;
}
