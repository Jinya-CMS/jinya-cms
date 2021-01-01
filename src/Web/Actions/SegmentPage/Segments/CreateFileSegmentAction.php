<?php

namespace App\Web\Actions\SegmentPage\Segments;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\File;
use App\Database\Segment;
use App\Database\SegmentPage;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Attributes\RequiredFields;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/segment-page/{id}/segment/file', JinyaAction::POST)]
#[Authenticated(Authenticated::WRITER)]
#[RequiredFields(['file', 'action', 'position'])]
class CreateFileSegmentAction extends Action
{

    /**
     * @inheritDoc
     * @return Response
     * @throws JsonException
     * @throws NoResultException
     * @throws UniqueFailedException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     */
    protected function action(): Response
    {
        $body = $this->request->getParsedBody();
        $id = $this->args['id'];
        $segmentPage = SegmentPage::findById($id);

        if (!$segmentPage) {
            throw new NoResultException($this->request, 'Segment page not found');
        }

        $fileId = $body['file'];
        if (!File::findById($fileId)) {
            throw new NoResultException($this->request, 'File not found');
        }

        $segment = new Segment();
        $segment->pageId = $id;
        $segment->fileId = $body['file'];
        $segment->galleryId = null;
        $segment->formId = null;
        $segment->html = null;
        $segment->script = $body['script'];
        $segment->target = $body['target'];
        $segment->action = $body['action'];
        $segment->position = $body['position'];

        $segment->create();

        return $this->respond($segment->format(), Action::HTTP_CREATED);
    }
}