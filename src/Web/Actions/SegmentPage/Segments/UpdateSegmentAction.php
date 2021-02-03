<?php

namespace App\Web\Actions\SegmentPage\Segments;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\File;
use App\Database\Form;
use App\Database\Gallery;
use App\Database\Segment;
use App\OpenApiGeneration\Attributes\OpenApiParameter;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiRequestBody;
use App\OpenApiGeneration\Attributes\OpenApiRequestExample;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpNotFoundException;

#[JinyaAction('/api/segment-page/{id}/segment/{position}', JinyaAction::PUT)]
#[Authenticated(Authenticated::WRITER)]
#[OpenApiRequest('This action updates the given segment')]
#[OpenApiRequestBody([
    'target' => ['type' => 'string'],
    'action' => ['type' => 'string'],
    'position' => ['type' => 'integer'],
    'file' => ['type' => 'integer'],
    'form' => ['type' => 'integer'],
    'html' => ['type' => 'string'],
    'gallery' => ['type' => 'integer'],
])]
#[OpenApiParameter('id', required: true, type: OpenApiParameter::TYPE_INTEGER)]
#[OpenApiParameter('position', required: true, type: OpenApiParameter::TYPE_INTEGER)]
#[OpenApiRequestExample('Update file segment', [
    'file' => 0,
    'position' => 0,
    'action' => 'link',
    'target' => OpenApiResponse::FAKER_URL,
])]
#[OpenApiRequestExample('Update form segment', [
    'form' => 0,
    'position' => 0,
])]
#[OpenApiRequestExample('Update gallery segment', [
    'gallery' => 0,
    'position' => 0,
])]
#[OpenApiRequestExample('Update html segment', [
    'html' => 0,
    'position' => 0,
])]
#[OpenApiResponse('Successfully updated the segment', statusCode: Action::HTTP_NO_CONTENT)]
#[OpenApiResponse('Segment page not found', example: OpenApiResponse::NOT_FOUND, exampleName: 'Segment page not found', statusCode: Action::HTTP_NOT_FOUND, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
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
