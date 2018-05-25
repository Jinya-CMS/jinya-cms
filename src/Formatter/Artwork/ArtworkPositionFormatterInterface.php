<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 18.02.2018
 * Time: 16:05.
 */

namespace Jinya\Formatter\Artwork;

use Jinya\Entity\ArtworkPosition;
use Jinya\Formatter\FormatterInterface;

interface ArtworkPositionFormatterInterface extends FormatterInterface
{
    /**
     * Initializes the format.
     *
     * @param ArtworkPosition $artworkPosition
     *
     * @return ArtworkPositionFormatterInterface
     */
    public function init(ArtworkPosition $artworkPosition): ArtworkPositionFormatterInterface;

    /**
     * Formats the artwork.
     *
     * @return ArtworkPositionFormatterInterface
     */
    public function artwork(): ArtworkPositionFormatterInterface;

    /**
     * Formats the gallery.
     *
     * @return ArtworkPositionFormatterInterface
     */
    public function gallery(): ArtworkPositionFormatterInterface;

    /**
     * Formats the position.
     *
     * @return ArtworkPositionFormatterInterface
     */
    public function position(): ArtworkPositionFormatterInterface;

    /**
     * Formats the id.
     *
     * @return ArtworkPositionFormatterInterface
     */
    public function id(): ArtworkPositionFormatterInterface;
}
