<?php

namespace App\Web\Actions\SegmentPage\Segments;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Segment;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpNotFoundException;

#[JinyaAction('/api/segment-page/{id}/segment/{position}', JinyaAction::DELETE)]
#[Authenticated(Authenticated::WRITER)]
class DeleteSegmentAction extends Action
{
    /**
     * {@inheritDoc}
     * @throws HttpNotFoundException
     * @throws JsonException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
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
