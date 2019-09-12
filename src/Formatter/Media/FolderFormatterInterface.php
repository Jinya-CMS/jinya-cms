<?php

namespace Jinya\Formatter\Media;

use Jinya\Entity\Media\Folder;
use Jinya\Formatter\FormatterInterface;

interface FolderFormatterInterface extends FormatterInterface
{
    /**
     * Initializes the formatting
     *
     * @param Folder $folder
     * @return FolderFormatterInterface
     */
    public function init(Folder $folder): self;

    /**
     * Formats the type
     *
     * @return FolderFormatterInterface
     */
    public function parent(): self;

    /**
     * Formats the name
     *
     * @return FolderFormatterInterface
     */
    public function name(): self;

    /**
     * Formats the files
     *
     * @return FolderFormatterInterface
     */
    public function files(): self;

    /**
     * Formats the folders
     *
     * @return FolderFormatterInterface
     */
    public function folders(): self;

    /**
     * Formats the created info
     *
     * @return FolderFormatterInterface
     */
    public function created(): self;

    /**
     * Formats the updated info
     *
     * @return FolderFormatterInterface
     */
    public function updated(): self;

    /**
     * Formats the history
     *
     * @return FolderFormatterInterface
     */
    public function history(): self;

    /**
     * Formats the tags
     *
     * @return FolderFormatterInterface
     */
    public function tags(): self;

    /**
     * Formats the id
     *
     * @return FolderFormatterInterface
     */
    public function id(): self;
}
