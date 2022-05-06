<?php

namespace App\Web\Actions\SegmentPage\Segments;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\File;
use App\Database\Form;
use App\Database\Gallery;
use App\Database\Segment;
use App\Web\Actions\Action;
use App\Web\Exceptions\NoResultException;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpNotFoundException;

/**
 *
 */
class UpdateSegmentAction extends Action
{
    /**
     * {@inheritDoc}
     * @return Response
     * @throws ForeignKeyFailedException
     * @throws HttpNotFoundException
     * @throws InvalidQueryException
     * @throws NoResultException
     * @throws UniqueFailedException
     * @throws \Jinya\PDOx\Exceptions\NoResultException
     */
    protected function action(): Response
    {

        $segment = Segment::findByPosition($this->args['id'], $this->args['position']);
        if (!$segment) {
            throw new HttpNotFoundException($this->request, 'Segment not found');
        }

        $galleryId = $this->body['gallery'] ?? '';
        $fileId = $this->body['file'] ?? '';
        $formId = $this->body['form'] ?? '';
        $html = $this->body['html'] ?? '';

        if ($segment->galleryId && $galleryId) {
            if (!Gallery::findById($galleryId)) {
                throw new NoResultException($this->request, 'Gallery not found');
            }
            $segment->galleryId = $galleryId;
            $segment->action = null;
            $segment->script = null;
            $segment->target = null;
        } elseif ($segment->formId && $formId) {
            if (!Form::findById($formId)) {
                throw new NoResultException($this->request, 'Form not found');
            }
            $segment->formId = $formId;
            $segment->action = null;
            $segment->script = null;
            $segment->target = null;
        } elseif ($segment->html && $html) {
            $segment->html = $html;
            $segment->action = null;
            $segment->script = null;
            $segment->target = null;
        } elseif ($segment->fileId && $fileId) {
            if (!File::findById($fileId)) {
                throw new NoResultException($this->request, 'File not found');
            }
            $segment->fileId = $fileId;
        }

        if ($segment->fileId) {
            $action = $this->body['action'] ?? '';
            if ($action === 'link') {
                $target = $this->body['target'] ?? '';
                $segment->target = $target;
                $segment->action = $action;
                $segment->script = null;
            } elseif ($action === 'script') {
                $script = $this->body['script'] ?? '';
                $segment->script = $script;
                $segment->action = $action;
                $segment->target = null;
            } elseif ($action === 'none') {
                $segment->action = $action;
                $segment->script = null;
                $segment->target = null;
            }
        }

        $segment->update();

        if (isset($this->body['newPosition'])) {
            $segment->move($this->body['newPosition']);
        }

        return $this->noContent();
    }
}
