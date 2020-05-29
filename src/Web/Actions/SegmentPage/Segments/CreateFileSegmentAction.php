<?php

namespace App\Web\Actions\SegmentPage\Segments;

use App\Database\Exceptions\UniqueFailedException;
use App\Database\File;
use App\Database\Segment;
use App\Database\SegmentPage;
use App\Web\Actions\Action;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class CreateFileSegmentAction extends Action
{

    /**
     * @inheritDoc
     * @return Response
     * @throws JsonException
     * @throws NoResultException
     * @throws UniqueFailedException
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