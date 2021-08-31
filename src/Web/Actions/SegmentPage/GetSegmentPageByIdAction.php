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
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/segment-page/{id}', JinyaAction::GET)]
#[Authenticated(Authenticated::READER)]
class GetSegmentPageByIdAction extends Action
{
    /**
     * {@inheritDoc}
     * @return Response
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     * @throws UniqueFailedException
     */
    public function action(): Response
    {
        $id = $this->args['id'];
        $segmentPage = SegmentPage::findById($id);
        if (null === $segmentPage) {
            throw new NoResultException($this->request, 'Segment page not found');
        }

        return $this->respond($segmentPage->format());
    }
}
