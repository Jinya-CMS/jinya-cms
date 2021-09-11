<?php

namespace App\Web\Actions\SegmentPage;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\SegmentPage;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/segment-page/{id}', JinyaAction::DELETE)]
#[Authenticated(Authenticated::WRITER)]
class DeleteSegmentPageAction extends Action
{
    /**
     * {@inheritDoc}
     * @throws JsonException
     * @throws NoResultException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    protected function action(): Response
    {
        $segmentPage = SegmentPage::findById($this->args['id']);
        if (null === $segmentPage) {
            throw new NoResultException($this->request, 'Segment page not found');
        }

        $segmentPage->delete();

        return $this->noContent();
    }
}
