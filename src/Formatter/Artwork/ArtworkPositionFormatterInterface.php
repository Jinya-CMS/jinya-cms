<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 18.02.2018
 * Time: 16:05
 */

namespace Jinya\Formatter\Artwork;

use Jinya\Entity\Artwork\ArtworkPosition;
use Jinya\Formatter\FormatterInterface;

interface ArtworkPositionFormatterInterface extends FormatterInterface
{
    /**
     * Initializes the format
     *
     * @param ArtworkPosition $artworkPosition
     * @return ArtworkPositionFormatterInterface
     */
    public function init(ArtworkPosition $artworkPosition): self;

    /**
     * Formats the artwork
     *
     * @return ArtworkPositionFormatterInterface
     */
    public function artwork(): self;

    /**
     * Formats the gallery
     *
     * @return ArtworkPositionFormatterInterface
     */
    public function gallery(): self;

    /**
     * Formats the position
     *
     * @return ArtworkPositionFormatterInterface
     */
    public function position(): self;

    /**
     * Formats the id
     *
     * @return ArtworkPositionFormatterInterface
     */
    public function id(): self;
}
