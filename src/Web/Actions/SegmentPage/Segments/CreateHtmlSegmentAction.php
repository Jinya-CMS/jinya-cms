<?php

namespace App\Web\Actions\SegmentPage\Segments;

use App\Database\Exceptions\UniqueFailedException;
use App\Database\Segment;
use App\Database\SegmentPage;
use App\Web\Actions\Action;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class CreateHtmlSegmentAction extends Action
{

    /**
     * @inheritDoc
     * @return Response
     * @throws JsonException
     * @throws NoResultException
     * @throws UniqueFailedException
     * @throws \App\Database\Exceptions\ForeignKeyFailedException
     * @throws \App\Database\Exceptions\InvalidQueryException
     */
    protected function action(): Response
    {
        $body = $this->request->getParsedBody();
        $id = $this->args['id'];
        $segmentPage = SegmentPage::findById($id);

        if (!$segmentPage) {
            throw new NoResultException($this->request, 'Segment page not found');
        }

        $segment = new Segment();
        $segment->pageId = $id;
        $segment->html = $body['html'];
        $segment->fileId = null;
        $segment->galleryId = null;
        $segment->formId = null;
        $segment->script = null;
        $segment->target = null;
        $segment->action = null;
        $segment->position = $body['position'];

        $segment->create();

        return $this->respond($segment->format(), Action::HTTP_CREATED);
    }
}