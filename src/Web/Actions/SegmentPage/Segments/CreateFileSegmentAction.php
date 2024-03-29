<?php

namespace App\Web\Actions\SegmentPage\Segments;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\File;
use App\Database\Segment;
use App\Database\SegmentPage;
use App\Web\Actions\Action;
use App\Web\Exceptions\NoResultException;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Action to create a file segment
 */
class CreateFileSegmentAction extends Action
{
    /**
     * Creates a new file segment with the information from the post body
     *
     * @return Response
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     * @throws UniqueFailedException
     * @throws \Jinya\PDOx\Exceptions\NoResultException
     */
    protected function action(): Response
    {

        $id = $this->args['id'];
        $segmentPage = SegmentPage::findById($id);

        if (!$segmentPage) {
            throw new NoResultException($this->request, 'Segment page not found');
        }

        $fileId = $this->body['file'];
        if (!File::findById($fileId)) {
            throw new NoResultException($this->request, 'File not found');
        }

        $segment = new Segment();
        $segment->pageId = $id;
        $segment->fileId = $this->body['file'];
        $segment->galleryId = null;
        $segment->formId = null;
        $segment->html = null;
        $segment->script = $this->body['script'] ?? '';
        $segment->target = $this->body['target'] ?? '';
        $segment->action = $this->body['action'] ?? '';
        $segment->position = $this->body['position'];

        $segment->create();

        return $this->respond($segment->format(), Action::HTTP_CREATED);
    }
}
