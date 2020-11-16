<?php

namespace Jinya\Formatter\Media;

use Jinya\Entity\Media\File;
use Jinya\Entity\Media\GalleryFilePosition;
use Jinya\Entity\Media\Tag;
use Jinya\Formatter\User\UserFormatterInterface;

class FileFormatter implements FileFormatterInterface
{
    private array $formattedData;

    private GalleryFilePositionFormatterInterface $galleryFilePositionFormatter;

    private UserFormatterInterface $userFormatter;

    private File $file;

    /**
     * FileFormatter constructor.
     */
    public function __construct(UserFormatterInterface $userFormatter)
    {
        $this->userFormatter = $userFormatter;
    }

    public function setGalleryFilePositionFormatter(
        GalleryFilePositionFormatterInterface $galleryFilePositionFormatter
    ): void {
        $this->galleryFilePositionFormatter = $galleryFilePositionFormatter;
    }

    /**
     * Initializes the formatting
     */
    public function init(File $file): FileFormatterInterface
    {
        $this->file = $file;
        $this->formattedData = [];

        return $this;
    }

    /**
     * Formats the type
     */
    public function type(): FileFormatterInterface
    {
        $this->formattedData['type'] = $this->file->getType();

        return $this;
    }

    /**
     * Formats the name
     */
    public function name(): FileFormatterInterface
    {
        $this->formattedData['name'] = $this->file->getName();

        return $this;
    }

    /**
     * Formats the created info
     */
    public function created(): FileFormatterInterface
    {
        $this->formattedData['created']['by'] = $this->userFormatter
            ->init($this->file->getCreator())
            ->profile()
            ->format();
        $this->formattedData['created']['at'] = $this->file->getCreatedAt()->format(DATE_ATOM);

        return $this;
    }

    /**
     * Formats the updated info
     */
    public function updated(): FileFormatterInterface
    {
        $this->formattedData['updated']['by'] = $this->userFormatter
            ->init($this->file->getUpdatedBy())
            ->profile()
            ->format();
        $this->formattedData['updated']['at'] = $this->file->getLastUpdatedAt()->format(DATE_ATOM);

        return $this;
    }

    /**
     * Formats the history
     */
    public function history(): FileFormatterInterface
    {
        $this->formattedData['history'] = $this->file->getHistory();

        return $this;
    }

    /**
     * Formats the tags
     */
    public function tags(): FileFormatterInterface
    {
        $this->formattedData['tags'] = $this->file->getTags()->map(static function (Tag $tag) {
            return [
                'tag' => $tag->getTag(),
                'id' => $tag->getId(),
            ];
        })->toArray();

        return $this;
    }

    /**
     * Formats the path
     */
    public function path(): FileFormatterInterface
    {
        $this->formattedData['path'] = $this->file->getPath();

        return $this;
    }

    /**
     * Formats the galleries
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
