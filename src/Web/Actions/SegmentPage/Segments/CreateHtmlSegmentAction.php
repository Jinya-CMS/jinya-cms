<?php

namespace App\Web\Actions\SegmentPage\Segments;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Segment;
use App\Database\SegmentPage;
use App\Web\Actions\Action;
use App\Web\Exceptions\NoResultException;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Action to create an HTML segment
 */
class CreateHtmlSegmentAction extends Action
{
    /**
     * Creates a new HTML segment with the given content
     *
     * @return Response
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     * @throws UniqueFailedException
     * @throws \Jinya\PDOx\Exceptions\NoResultException
     * @throws \Jinya\PDOx\Exceptions\NoResultException
     */
    protected function action(): Response
    {

        $id = $this->args['id'];
        $segmentPage = SegmentPage::findById($id);

        if (!$segmentPage) {
            throw new NoResultException($this->request, 'Segment page not found');
        }

        $segment = new Segment();
        $segment->pageId = $id;
        $segment->html = $this->body['html'];
        $segment->fileId = null;
        $segment->galleryId = null;
        $segment->formId = null;
        $segment->script = null;
        $segment->target = null;
        $segment->action = null;
        $segment->position = $this->body['position'];

        $segment->create();

        return $this->respond($segment->format(), Action::HTTP_CREATED);
    }
}
