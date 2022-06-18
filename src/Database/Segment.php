<?php

namespace App\Database;

use Iterator;
use JetBrains\PhpStorm\ArrayShape;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;
use RuntimeException;

/**
 * This class contains a segment of segment pages. Segments can contain different kinds of data, currently supported are galleries, forms, files and static HTML content. If the data is a file it is also possible to specify an action which can be either script or link. Depending on the action the fields have to be set accordingly.
 */
class Segment extends Utils\RearrangableEntity
{
    /** @var int The ID of the segment page this segment belongs to */
    public int $pageId;
    /** @var int|null The ID of the form this segment contains */
    public ?int $formId;
    /** @var int|null The ID of the gallery this segment contains */
    public ?int $galleryId;
    /** @var int|null The ID of the file this segment contains */
    public ?int $fileId;
    /** @var string|null The action type to execute when the file is clicked. It can be either script, link or none */
    public ?string $action;
    /** @var string|null The script to execute when the file is clicked */
    public ?string $script;
    /** @var string|null The link to open when the file is clicked */
    public ?string $target;
    /** @var string|null The HTML content of the segment */
    public ?string $html;

    /**
     * Not implemented
     */
    public static function findById(int $id): ?object
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * Not implemented
     */
    public static function findByKeyword(string $keyword): Iterator
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * Not implemented
     */
    public static function findAll(): Iterator
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * Gets the segment at the given position in the given page
     *
     * @param int $id
     * @param int $position
     * @return Segment|null
     * @throws Exceptions\ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     * @throws NoResultException
     * @throws NoResultException
     */
    public static function findByPosition(int $id, int $position): ?Segment
    {
        $sql = 'SELECT id, page_id, form_id, gallery_id, file_id, position, html, action, script, target FROM segment WHERE page_id = :id AND position = :position';

        try {
            return self::getPdo()->fetchObject($sql, new self(), [
                'id' => $id,
                'position' => $position
            ]);
        } catch (InvalidQueryException$exception) {
            throw self::convertInvalidQueryExceptionToException($exception);
        }
    }

    /**
     * Gets the file
     *
     * @return File|null
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public function getFile(): ?File
    {
        return File::findById($this->fileId);
    }

    /**
     * Gets the form
     *
     * @return Form|null
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public function getForm(): ?Form
    {
        return Form::findById($this->formId);
    }

    /**
     * Gets the gallery
     *
     * @return Gallery|null
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public function getGallery(): ?Gallery
    {
        return Gallery::findById($this->galleryId);
    }

    /**
     * Gets the corresponding segment page
     *
     * @return SegmentPage|null
     *
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public function getSegmentPage(): ?SegmentPage
    {
        return SegmentPage::findById($this->pageId);
    }

    /**
     * Formats the segment into an array
     *
     * @return array<string, array<string, int|string|null>|int|string|null>
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    #[ArrayShape([
        'position' => 'int',
        'id' => 'int',
        'form' => 'array',
        'gallery' => 'array',
        'target' => 'null|string',
        'html' => 'null|string',
        'script' => 'null|string',
        'action' => 'null|string',
        'file' => 'array'
    ])] public function format(): array
    {
        $data = [
            'position' => $this->position,
            'id' => $this->getIdAsInt(),
        ];
        if (isset($this->formId)) {
            $form = Form::findById($this->formId);
            $data['form'] = [
                'id' => $this->formId,
                'title' => $form?->title,
                'description' => $form?->description,
                'toAddress' => $form?->toAddress,
            ];
        } elseif (isset($this->galleryId)) {
            $gallery = Gallery::findById($this->galleryId);
            $data['gallery'] = [
                'id' => $this->galleryId,
                'name' => $gallery?->name,
                'description' => $gallery?->description,
                'type' => $gallery?->type,
                'orientation' => $gallery?->orientation,
            ];
        } elseif (isset($this->fileId)) {
            $file = File::findById($this->fileId);
            $data['file'] = [
                'id' => $this->fileId,
                'name' => $file?->name,
                'type' => $file?->type,
                'path' => $file?->path,
            ];
            $data['action'] = $this->action;
            $data['script'] = $this->script;
            $data['target'] = $this->target;
        } else {
            $data['html'] = $this->html;
        }

        return $data;
    }

    /**
     * Creates the current segment and also rearranges the other segments in the page based on the position
     *
     * @return void
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function create(): void
    {
        $this->internalRearrange('segment', 'page_id', $this->pageId, $this->position);
        $this->internalCreate('segment');
        $this->resetOrder('segment', 'page_id', $this->pageId);
    }

    /**
     * Deletes the given segment, the order of the remaining segments will be reset
     *
     * @return void
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function delete(): void
    {
        $this->internalDelete('segment');
        $this->internalRearrange('segment', 'page_id', $this->pageId, -1);
        $this->resetOrder('segment', 'page_id', $this->pageId);
    }

    /**
     * Moves the segment to a new position and rearranges the other items accordingly
     *
     * @param int $newPosition
     * @return void
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function move(int $newPosition): void
    {
        $this->internalRearrange('segment', 'page_id', $this->pageId, $newPosition);
        $this->update();
        $this->resetOrder('segment', 'page_id', $this->pageId);
    }

    /**
     * Updates the current menu item
     *
     * @return void
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function update(): void
    {
        $this->internalUpdate('segment');
    }
}