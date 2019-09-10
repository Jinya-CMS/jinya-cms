<?php

namespace Jinya\Formatter\Media;

use Jinya\Entity\Media\File;
use Jinya\Entity\Media\Folder;
use Jinya\Entity\Media\GalleryFilePosition;
use Jinya\Entity\Media\Tag;
use Jinya\Formatter\User\UserFormatterInterface;

class FolderFormatter implements FolderFormatterInterface
{
    /** @var array */
    private $formattedData;

    /** @var GalleryFilePositionFormatterInterface */
    private $galleryFilePositionFormatter;

    /** @var UserFormatterInterface */
    private $userFormatter;

    /** @var FileFormatterInterface */
    private $fileFormatter;

    /** @var Folder */
    private $folder;

    /**
     * FolderFormatter constructor.
     * @param UserFormatterInterface $userFormatter
     */
    public function __construct(UserFormatterInterface $userFormatter)
    {
        $this->userFormatter = $userFormatter;
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
     * @param FileFormatterInterface $fileFormatter
     */
    public function setFileFormatter(FileFormatterInterface $fileFormatter): void
    {
        $this->fileFormatter = $fileFormatter;
    }

    /**
     * Formats the name
     *
     * @return FolderFormatterInterface
     */
    public function name(): FolderFormatterInterface
    {
        $this->formattedData['name'] = $this->folder->getName();

        return $this;
    }

    /**
     * Formats the created info
     *
     * @return FolderFormatterInterface
     */
    public function created(): FolderFormatterInterface
    {
        $this->formattedData['created']['by'] = $this->userFormatter
            ->init($this->folder->getCreator())
            ->profile()
            ->format();
        $this->formattedData['created']['at'] = $this->folder->getCreatedAt();

        return $this;
    }

    /**
     * Formats the updated info
     *
     * @return FolderFormatterInterface
     */
    public function updated(): FolderFormatterInterface
    {
        $this->formattedData['updated']['by'] = $this->userFormatter
            ->init($this->folder->getUpdatedBy())
            ->profile()
            ->format();
        $this->formattedData['updated']['at'] = $this->folder->getLastUpdatedAt();

        return $this;
    }

    /**
     * Formats the history
     *
     * @return FolderFormatterInterface
     */
    public function history(): FolderFormatterInterface
    {
        $this->formattedData['history'] = $this->folder->getHistory();

        return $this;
    }

    /**
     * Formats the tags
     *
     * @return FolderFormatterInterface
     */
    public function tags(): FolderFormatterInterface
    {
        $this->formattedData['tags'] = $this->folder->getTags()->map(static function (Tag $tag) {
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
     * @return FolderFormatterInterface
     */
    public function path(): FolderFormatterInterface
    {
        $this->formattedData['path'] = $this->folder->getPath();

        return $this;
    }

    /**
     * Formats the galleries
     *
     * @return FolderFormatterInterface
     */
    public function galleries(): FolderFormatterInterface
    {
        $this->formattedData['galleries'] = $this->folder->getGalleries()->map(function (
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
     * @return FolderFormatterInterface
     */
    public function id(): FolderFormatterInterface
    {
        $this->formattedData['id'] = $this->folder->getId();

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

    /**
     * Formats the type
     *
     * @return FolderFormatterInterface
     */
    public function parent(): FolderFormatterInterface
    {
        $folderFormatter = new self();

        $this->formattedData['parent'] = $folderFormatter
            ->init($this->folder->getParent())
            ->id()
            ->name()
            ->path()
            ->tags();

        return $this;
    }

    /**
     * Initializes the formatting
     *
     * @param Folder $folder
     * @return FolderFormatterInterface
     */
    public function init(Folder $folder): FolderFormatterInterface
    {
        $this->folder = $folder;
        $this->formattedData = [];

        return $this;
    }

    /**
     * Formats the files
     *
     * @return FolderFormatterInterface
     */
    public function files(): FolderFormatterInterface
    {
        $this->formattedData['folders'] = $this->folder->getChildFolders()->map(
            static function (File $file) {
                $this->fileFormatter->init($file)
                    ->id()
                    ->name()
                    ->path()
                    ->tags()
                    ->type()
                    ->created()
                    ->updated();
            }
        );

        return $this;
    }

    /**
     * Formats the folders
     *
     * @return FolderFormatterInterface
     */
    public function folders(): FolderFormatterInterface
    {
        $folderFormatter = new self();

        $this->formattedData['folders'] = $this->folder->getChildFolders()->map(
            static function (Folder $fold) use ($folderFormatter) {
                $folderFormatter->init($fold)
                    ->id()
                    ->name()
                    ->path()
                    ->tags();
            }
        );

        return $this;
    }
}
