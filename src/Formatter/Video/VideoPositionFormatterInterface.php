<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 18.02.2018
 * Time: 16:05
 */

namespace Jinya\Formatter\Video;

use Jinya\Entity\Video\VideoPosition;
use Jinya\Formatter\FormatterInterface;

interface VideoPositionFormatterInterface extends FormatterInterface
{
    /**
     * Initializes the format
     *
     * @param VideoPosition $videoPosition
     * @return VideoPositionFormatterInterface
     */
    public function init(VideoPosition $videoPosition): self;

    /**
     * Formats the video
     *
     * @return VideoPositionFormatterInterface
     */
    public function video(): self;

    /**
     * Formats the gallery
     *
     * @return VideoPositionFormatterInterface
     */
    public function gallery(): self;

    /**
     * Formats the position
     *
     * @return VideoPositionFormatterInterface
     */
    public function position(): self;

    /**
     * Formats the id
     *
     * @return VideoPositionFormatterInterface
     */
    public function id(): self;
}
