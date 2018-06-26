<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 02.06.18
 * Time: 16:45
 */

namespace Jinya\Formatter\Video;

use Jinya\Entity\Video\Video;
use Jinya\Formatter\FormatterInterface;

interface VideoFormatterInterface extends FormatterInterface
{
    /**
     * Initializes the formatting
     *
     * @param Video $Video
     * @return VideoFormatterInterface
     */
    public function init(Video $Video): self;

    /**
     * Formats the video key
     *
     * @return VideoFormatterInterface
     */
    public function video(): self;

    /**
     * Formats the name
     *
     * @return VideoFormatterInterface
     */
    public function name(): self;

    /**
     * Formats the slug
     *
     * @return VideoFormatterInterface
     */
    public function slug(): self;

    /**
     * Formats the description
     *
     * @return VideoFormatterInterface
     */
    public function description(): self;

    /**
     * Formats the created info
     *
     * @return VideoFormatterInterface
     */
    public function created(): self;

    /**
     * Formats the updated info
     *
     * @return VideoFormatterInterface
     */
    public function updated(): self;

    /**
     * Formats the history
     *
     * @return VideoFormatterInterface
     */
    public function history(): self;

    /**
     * Formats the poster
     *
     * @return VideoFormatterInterface
     */
    public function poster(): self;

    /**
     * Formats the galleries
     *
     * @return VideoFormatterInterface
     */
    public function galleries(): self;

}
