<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 02.06.18
 * Time: 16:45
 */

namespace Jinya\Formatter\Video;


use Jinya\Entity\Video\YoutubeVideo;
use Jinya\Formatter\FormatterInterface;

interface YoutubeVideoFormatterInterface extends FormatterInterface
{
    /**
     * Initializes the formatting
     *
     * @param YoutubeVideo $youtubeVideo
     * @return YoutubeVideoFormatterInterface
     */
    public function init(YoutubeVideo $youtubeVideo): self;

    /**
     * Formats the video key
     *
     * @return YoutubeVideoFormatterInterface
     */
    public function videoKey(): self;

    /**
     * Formats the name
     *
     * @return YoutubeVideoFormatterInterface
     */
    public function name(): self;

    /**
     * Formats the slug
     *
     * @return YoutubeVideoFormatterInterface
     */
    public function slug(): self;

    /**
     * Formats the description
     *
     * @return YoutubeVideoFormatterInterface
     */
    public function description(): self;

    /**
     * Formats the created info
     *
     * @return YoutubeVideoFormatterInterface
     */
    public function created(): self;

    /**
     * Formats the updated info
     *
     * @return YoutubeVideoFormatterInterface
     */
    public function updated(): self;

    /**
     * Formats the history
     *
     * @return YoutubeVideoFormatterInterface
     */
    public function history(): self;
}