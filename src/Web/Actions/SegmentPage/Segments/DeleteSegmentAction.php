<?php

namespace App\Web\Actions\SegmentPage\Segments;

use App\Database\Segment;
use App\Web\Actions\Action;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpNotFoundException;

class DeleteSegmentAction extends Action
{

    /**
     * @inheritDoc
     * @return Response
     * @throws HttpNotFoundException
     * @throws JsonException
     * @throws \App\Database\Exceptions\ForeignKeyFailedException
     * @throws \App\Database\Exceptions\InvalidQueryException
     * @throws \App\Database\Exceptions\UniqueFailedException
     */
    protected function action(): Response
    {
        $segment = Segment::findByPosition($this->args['id'], $this->args['position']);
        if (!$segment) {
            throw new HttpNotFoundException($this->request, 'SegmentPage item not found');
        }

        $segment->delete();

        return $this->noContent();
    }
}