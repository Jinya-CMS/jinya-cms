<?php

namespace App\Web\Actions\SegmentPage\Segments;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\File;
use App\Database\Form;
use App\Database\Gallery;
use App\Database\Segment;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpNotFoundException;

#[JinyaAction('/api/segment-page/{id}/segment/{position}', JinyaAction::PUT)]
#[Authenticated(Authenticated::WRITER)]
class UpdateSegmentAction extends Action
{
    /**
     * {@inheritDoc}
     * @throws HttpNotFoundException
     * @throws JsonException
     * @throws NoResultException
     * @throws UniqueFailedException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     */
    protected function action(): Response
    {
        $body = $this->request->getParsedBody();
        $segment = Segment::findByPosition($this->args['id'], $this->args['position']);
        if (!$segment) {
            throw new HttpNotFoundException($this->request, 'Segment not found');
        }

        $galleryId = $body['gallery'] ?? '';
        $fileId = $body['file'] ?? '';
        $formId = $body['form'] ?? '';
        $html = $body['html'] ?? '';

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
            $action = $body['action'] ?? '';
            if ('link' === $action) {
                $target = $body['target'] ?? '';
                $segment->target = $target;
                $segment->action = $action;
                $segment->script = null;
            } elseif ('script' === $action) {
                $script = $body['script'] ?? '';
                $segment->script = $script;
                $segment->action = $action;
                $segment->target = null;
            } elseif ('none' === $action) {
                $segment->action = $action;
                $segment->script = null;
                $segment->target = null;
            }
        }

        $segment->update();

        if (isset($body['newPosition'])) {
            $segment->move($body['newPosition']);
        }

        return $this->noContent();
    }
}
