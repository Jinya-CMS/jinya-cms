<?php

namespace App\Web\Actions\SegmentPage\Segments;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\SegmentPage;
use App\Web\Actions\Action;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Action to get all segments
 */
class GetSegmentsAction extends Action
{
    /**
     * Gets all segments for the given segment page
     *
     * @return Response
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     * @throws NoResultException
     */
    protected function action(): Response
    {
        $id = $this->args['id'];
        $segmentPage = SegmentPage::findById($id);
        if ($segmentPage === null) {
            throw new \App\Web\Exceptions\NoResultException($this->request, 'Segment page not found');
        }
        $segments = $segmentPage->getSegments();

        return $this->respond($this->formatIterator($segments));
    }
}
