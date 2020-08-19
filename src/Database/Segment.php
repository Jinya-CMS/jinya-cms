<?php

namespace App\Database;

use Exception;
use Iterator;
use Laminas\Db\Sql\Predicate\PredicateSet;
use RuntimeException;

class Segment extends Utils\RearrangableEntity implements Utils\FormattableEntityInterface
{

    public int $pageId;
    public ?int $formId;
    public ?int $galleryId;
    public ?int $fileId;
    public ?string $action;
    public ?string $script;
    public ?string $target;
    public ?string $html;
    public int $position;

    /**
     * @inheritDoc
     */
    public static function findById(int $id)
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
     */
    public static function findByPosition(int $id, int $position): ?Segment
    {
        $sql = self::getSql();
        $select = $sql
            ->select()
            ->from('segment')
            ->where(['page_id = :id', 'position = :position'], PredicateSet::OP_AND);

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return self::hydrateSingleResult($sql->prepareStatementForSqlObject($select)->execute([
            'id' => $id,
            'position' => $position
        ]), new self());
    }

    /**
     * Gets the file
     *
     * @return File|null
     */
    public function getFile(): ?File
    {
        return File::findById($this->fileId);
    }

    /**
     * Gets the form
     *
     * @return Form|null
     */
    public function getForm(): ?Form
    {
        return Form::findById($this->formId);
    }

    /**
     * Gets the gallery
     *
     * @return Gallery|null
     */
    public function getGallery(): ?Gallery
    {
        return Gallery::findById($this->galleryId);
    }

    /**
     * Gets the corresponding segment page
     *
     * @return SegmentPage
     */
    public function getSegmentPage(): SegmentPage
    {
        return SegmentPage::findById($this->pageId);
    }

    public function format(): array
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
     * @throws Exception
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