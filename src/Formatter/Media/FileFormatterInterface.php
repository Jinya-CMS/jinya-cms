<?php

namespace Jinya\Formatter\Media;

use Jinya\Entity\Media\File;
use Jinya\Formatter\FormatterInterface;

interface FileFormatterInterface extends FormatterInterface
{
    /**
     * Initializes the formatting
     *
     * @return FileFormatterInterface
     */
    public function init(File $file): self;

    /**
     * Formats the type
     *
     * @return FileFormatterInterface
     */
    public function type(): self;

    /**
     * Formats the name
     *
     * @return FileFormatterInterface
     */
    public function name(): self;

    /**
     * Formats the created info
     *
     * @return FileFormatterInterface
     */
    public function created(): self;

    /**
     * Formats the updated info
     *
     * @return FileFormatterInterface
     */
    public function updated(): self;

    /**
     * Formats the history
     *
     * @return FileFormatterInterface
     */
    public function history(): self;

    /**
     * Formats the tags
     *
     * @return FileFormatterInterface
     */
    public function tags(): self;

    /**
     * Formats the path
     *
     * @return FileFormatterInterface
     */
    public function path(): self;

    /**
     * Formats the galleries
     *
     * @return FileFormatterInterface
     */
    public function galleries(): self;

    /**
     * Formats the id
     *
     * @return FileFormatterInterface
     */
    public function id(): self;
}
