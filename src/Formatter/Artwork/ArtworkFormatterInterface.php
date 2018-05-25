<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 18.02.2018
 * Time: 15:46
 */

namespace Jinya\Formatter\Artwork;

use Jinya\Entity\Artwork;
use Jinya\Formatter\FormatterInterface;

interface ArtworkFormatterInterface extends FormatterInterface
{
    /**
     * Initializes the formatting
     *
     * @param Artwork $artwork
     *
     * @return ArtworkFormatterInterface
     */
    public function init(Artwork $artwork): self;

    /**
     * Formats the slug
     *
     * @return ArtworkFormatterInterface
     */
    public function slug(): self;

    /**
     * Formats the name
     *
     * @return ArtworkFormatterInterface
     */
    public function name(): self;

    /**
     * Formats the description
     *
     * @return ArtworkFormatterInterface
     */
    public function description(): self;

    /**
     * Formats the created info
     *
     * @return ArtworkFormatterInterface
     */
    public function created(): self;

    /**
     * Formats the updated info
     *
     * @return ArtworkFormatterInterface
     */
    public function updated(): self;

    /**
     * Formats the history
     *
     * @return ArtworkFormatterInterface
     */
    public function history(): self;

    /**
     * Formats the picture
     *
     * @return ArtworkFormatterInterface
     */
    public function picture(): self;

    /**
     * Formats the labels
     *
     * @return ArtworkFormatterInterface
     */
    public function labels(): self;

    /**
     * Formats the galleries
     *
     * @return ArtworkFormatterInterface
     */
    public function galleries(): self;

    /**
     * Formats the id
     *
     * @return ArtworkFormatterInterface
     */
    public function id(): self;
}
