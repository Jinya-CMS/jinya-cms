<?php

namespace App\Database;

use App\OpenApiGeneration\Attributes\OpenApiField;
use App\OpenApiGeneration\Attributes\OpenApiHiddenField;
use App\OpenApiGeneration\Attributes\OpenApiModel;
use Iterator;
use JetBrains\PhpStorm\ArrayShape;
use RuntimeException;

#[OpenApiModel('A segment is a part of a segment page')]
class Segment extends Utils\RearrangableEntity implements Utils\FormattableEntityInterface
{
    #[OpenApiHiddenField]
    public int $pageId;
    #[OpenApiField(required: false, structure: [
        'id' => ['type' => 'integer'],
        'title' => ['type' => 'string'],
        'description' => ['type' => 'string'],
        'toAddress' => ['type' => 'string', 'format' => 'email'],
    ], name: 'form')]
    public ?int $formId;
    #[OpenApiField(required: false, structure: [
        'id' => ['type' => 'integer'],
        'name' => ['type' => 'string'],
        'description' => ['type' => 'string'],
        'type' => ['type' => 'string', 'enum' => [Gallery::TYPE_SEQUENCE, Gallery::TYPE_MASONRY]],
        'orientation' => [
            'type' => 'string',
            'enum' => [Gallery::ORIENTATION_VERTICAL, Gallery::ORIENTATION_HORIZONTAL]
        ],
    ], name: 'gallery')]
    public ?int $galleryId;
    #[OpenApiField(required: false, structure: [
        'id' => ['type' => 'integer'],
        'name' => ['type' => 'string'],
        'type' => ['type' => 'string'],
        'path' => ['type' => 'string'],
    ], name: 'file')]
    public ?int $fileId;
    #[OpenApiField(required: false, enumValues: ['link', 'none'])]
    public ?string $action;
    #[OpenApiHiddenField]
    public ?string $script;
    #[OpenApiField(required: false, defaultValue: '')]
    public ?string $target;
    #[OpenApiField(required: false, defaultValue: '')]
    public ?string $html;

    /**
     * @inheritDoc
     */
    public static function findById(int $id): ?object
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * @inheritDoc
     */
    public static function findByKeyword(string $keyword): Iterator
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * @inheritDoc
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
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    public static function findByPosition(int $id, int $position): ?Segment
    {
        $sql = 'SELECT id, page_id, form_id, gallery_id, file_id, position, html, action, script, target FROM segment WHERE page_id = :id AND position = :position';
        $result = self::executeStatement(
            $sql,
            [
                'id' => $id,
                'position' => $position
            ]
        );

        if (count($result) === 0) {
            return null;
        }

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return self::hydrateSingleResult($result[0], new self());
    }

    /**
     * Gets the file
     *
     * @return File|null
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
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
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
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
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
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
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    public function getSegmentPage(): ?SegmentPage
    {
        return SegmentPage::findById($this->pageId);
    }

    /**
     * @throws Exceptions\UniqueFailedException
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     */
    #[ArrayShape([
        'position' => "int",
        'id' => "int",
        'form' => "array",
        'gallery' => "array",
        'target' => "null|string",
        'html' => "null|string",
        'script' => "null|string",
        'action' => "null|string",
        'file' => "array"
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
                'title' => $form->title,
                'description' => $form->description,
                'toAddress' => $form->toAddress,
            ];
        } elseif (isset($this->galleryId)) {
            $gallery = Gallery::findById($this->galleryId);
            $data['gallery'] = [
                'id' => $this->galleryId,
                'name' => $gallery->name,
                'description' => $gallery->description,
                'type' => $gallery->type,
                'orientation' => $gallery->orientation,
            ];
        } elseif (isset($this->fileId)) {
            $file = File::findById($this->fileId);
            $data['file'] = [
                'id' => $this->fileId,
                'name' => $file->name,
                'type' => $file->type,
                'path' => $file->path,
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
     * @inheritDoc
     */
    public function create(): void
    {
        $this->internalRearrange('segment', 'page_id', $this->pageId, $this->position);
        $this->internalCreate('segment');
    }

    /**
     * @inheritDoc
     */
    public function delete(): void
    {
        $this->internalDelete('segment');
        $this->internalRearrange('segment', 'page_id', $this->pageId, -1);
    }

    /**
     * @inheritDoc
     */
    public function update(): void
    {
        $this->internalUpdate('segment');
    }

    /**
     * @inheritDoc
     */
    public function move(int $newPosition): void
    {
        $this->internalRearrange('segment', 'page_id', $this->pageId, $newPosition);
        parent::move($newPosition);
    }
}