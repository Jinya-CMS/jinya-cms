<?php

namespace App\Web\Actions\SegmentPage\Segments;

use App\Database\SegmentPage;
use App\Web\Actions\Action;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class GetSegmentsAction extends Action
{

    /**
     * @inheritDoc
     * @return Response
     * @throws JsonException
     * @throws \App\Database\Exceptions\ForeignKeyFailedException
     * @throws \App\Database\Exceptions\InvalidQueryException
     * @throws \App\Database\Exceptions\UniqueFailedException
     */
    protected function action(): Response
    {
        $id = $this->args['id'];
        $segments = SegmentPage::findById($id)->getSegments();

        return $this->respond($this->formatIterator($segments));
    }
}