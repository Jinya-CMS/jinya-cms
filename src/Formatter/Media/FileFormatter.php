<?php

namespace Jinya\Formatter\Media;

use Jinya\Entity\Media\File;
use Jinya\Entity\Media\GalleryFilePosition;
use Jinya\Entity\Media\Tag;
use Jinya\Formatter\User\UserFormatterInterface;

class FileFormatter implements FileFormatterInterface
{
    /** @var array */
    private $formattedData;

    /** @var FolderFormatterInterface */
    private $folderFormatter;

    /** @var GalleryFilePositionFormatterInterface */
    private $galleryFilePositionFormatter;

    /** @var UserFormatterInterface */
    private $userFormatter;

    /** @var File */
    private $file;

    /**
     * FileFormatter constructor.
     * @param UserFormatterInterface $userFormatter
     */
    public function __construct(UserFormatterInterface $userFormatter)
    {
        $this->userFormatter = $userFormatter;
    }

    /**
     * @param FolderFormatterInterface $folderFormatter
     */
    public function setFolderFormatter(FolderFormatterInterface $folderFormatter): void
    {
        $this->folderFormatter = $folderFormatter;
    }

    /**
     * @param GalleryFilePositionFormatterInterface $galleryFilePositionFormatter
     */
    public function setGalleryFilePositionFormatter(
        GalleryFilePositionFormatterInterface $galleryFilePositionFormatter
    ): void {
        $this->galleryFilePositionFormatter = $galleryFilePositionFormatter;
    }

    /**
     * Initializes the formatting
     *
     * @param File $file
     * @return FileFormatterInterface
     */
    public function init(File $file): FileFormatterInterface
    {
        $this->file = $file;
        $this->formattedData = [];

        return $this;
    }

    /**
     * Formats the type
     *
     * @return FileFormatterInterface
     */
    public function type(): FileFormatterInterface
    {
        $this->formattedData['type'] = $this->file->getType();

        return $this;
    }

    /**
     * Formats the name
     *
     * @return FileFormatterInterface
     */
    public function name(): FileFormatterInterface
    {
        $this->formattedData['name'] = $this->file->getName();

        return $this;
    }

    /**
     * Formats the folder
     *
     * @return FileFormatterInterface
     */
    public function folder(): FileFormatterInterface
    {
        $this->formattedData['folder'] = $this->folderFormatter
            ->init($this->file->getFolder())
            ->id()
            ->name()
            ->path()
            ->tags();

        return $this;
    }

    /**
     * Formats the created info
     *
     * @return FileFormatterInterface
     */
    public function created(): FileFormatterInterface
    {
        $this->formattedData['created']['by'] = $this->userFormatter
            ->init($this->file->getCreator())
            ->profile()
            ->format();
        $this->formattedData['created']['at'] = $this->file->getCreatedAt();

        return $this;
    }

    /**
     * Formats the updated info
     *
     * @return FileFormatterInterface
     */
    public function updated(): FileFormatterInterface
    {
        $this->formattedData['updated']['by'] = $this->userFormatter
            ->init($this->file->getUpdatedBy())
            ->profile()
            ->format();
        $this->formattedData['updated']['at'] = $this->file->getLastUpdatedAt();

        return $this;
    }

    /**
     * Formats the history
     *
     * @return FileFormatterInterface
     */
    public function history(): FileFormatterInterface
    {
        $this->formattedData['history'] = $this->file->getHistory();

        return $this;
    }

    /**
     * Formats the tags
     *
     * @return FileFormatterInterface
     */
    public function tags(): FileFormatterInterface
    {
        $this->formattedData['tags'] = $this->file->getTags()->map(static function (Tag $tag) {
            return [
                'tag' => $tag->getTag(),
                'id' => $tag->getId(),
            ];
        });

        return $this;
    }

    /**
     * Formats the path
     *
     * @return FileFormatterInterface
     */
    public function path(): FileFormatterInterface
    {
        $this->formattedData['path'] = $this->file->getPath();

        return $this;
    }

    /**
     * Formats the galleries
     *
     * @return FileFormatterInterface
     */
    public function galleries(): FileFormatterInterface
    {
        $this->formattedData['galleries'] = $this->file->getGalleries()->map(function (
            GalleryFilePosition $filePosition
        ) {
            return $this->galleryFilePositionFormatter
                ->init($filePosition)
                ->id()
                ->position()
                ->format();
        });

        return $this;
    }

    /**
     * Formats the id
     *
     * @return FileFormatterInterface
     */
    public function id(): FileFormatterInterface
    {
        $this->formattedData['id'] = $this->file->getId();

        return $this;
    }

    /**
     * Formats the content of the @return array
     * @see FormatterInterface into an array
     */
    public function format(): array
    {
        return $this->formattedData;
    }
}
