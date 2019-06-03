<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 02.06.18
 * Time: 16:48
 */

namespace Jinya\Formatter\Video;

use Jinya\Entity\Video\YoutubeVideo;
use Jinya\Formatter\User\UserFormatterInterface;

class YoutubeVideoFormatter implements YoutubeVideoFormatterInterface
{
    /** @var array */
    private $formattedData;

    /** @var YoutubeVideo */
    private $youtubeVideo;

    /** @var UserFormatterInterface */
    private $userFormatter;

    /**
     * YoutubeVideoFormatter constructor.
     * @param UserFormatterInterface $userFormatter
     */
    public function __construct(UserFormatterInterface $userFormatter)
    {
        $this->userFormatter = $userFormatter;
    }

    /**
     * Formats the content of the @return array
     * @see FormatterInterface into an array
     */
    public function format(): array
    {
        return $this->formattedData;
    }

    /**
     * Initializes the formatting
     *
     * @param YoutubeVideo $youtubeVideo
     * @return YoutubeVideoFormatterInterface
     */
    public function init(YoutubeVideo $youtubeVideo): YoutubeVideoFormatterInterface
    {
        $this->youtubeVideo = $youtubeVideo;

        return $this;
    }

    /**
     * Formats the video key
     *
     * @return YoutubeVideoFormatterInterface
     */
    public function videoKey(): YoutubeVideoFormatterInterface
    {
        $this->formattedData['videoKey'] = $this->youtubeVideo->getVideoKey();

        return $this;
    }

    /**
     * Formats the name
     *
     * @return YoutubeVideoFormatterInterface
     */
    public function name(): YoutubeVideoFormatterInterface
    {
        $this->formattedData['name'] = $this->youtubeVideo->getName();

        return $this;
    }

    /**
     * Formats the slug
     *
     * @return YoutubeVideoFormatterInterface
     */
    public function slug(): YoutubeVideoFormatterInterface
    {
        $this->formattedData['slug'] = $this->youtubeVideo->getSlug();

        return $this;
    }

    /**
     * Formats the description
     *
     * @return YoutubeVideoFormatterInterface
     */
    public function description(): YoutubeVideoFormatterInterface
    {
        $this->formattedData['description'] = $this->youtubeVideo->getDescription();

        return $this;
    }

    /**
     * Formats the created info
     *
     * @return YoutubeVideoFormatterInterface
     */
    public function created(): YoutubeVideoFormatterInterface
    {
        $this->formattedData['created']['by'] = $this->userFormatter
            ->init($this->youtubeVideo->getCreator())
            ->profile()
            ->format();
        $this->formattedData['created']['at'] = $this->youtubeVideo->getCreatedAt();

        return $this;
    }

    /**
     * Formats the updated info
     *
     * @return YoutubeVideoFormatterInterface
     */
    public function updated(): YoutubeVideoFormatterInterface
    {
        $this->formattedData['updated']['by'] = $this->userFormatter
            ->init($this->youtubeVideo->getUpdatedBy())
            ->profile()
            ->format();
        $this->formattedData['updated']['at'] = $this->youtubeVideo->getLastUpdatedAt();

        return $this;
    }

    /**
     * Formats the history
     *
     * @return YoutubeVideoFormatterInterface
     */
    public function history(): YoutubeVideoFormatterInterface
    {
        $this->formattedData['history'] = $this->youtubeVideo->getHistory();

        return $this;
    }
}
