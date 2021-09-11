<?php

namespace App\Web\Actions\SegmentPage;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\SegmentPage;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\ConflictException;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/segment-page/{id}', JinyaAction::PUT)]
#[Authenticated(Authenticated::WRITER)]
class UpdateSegmentPageAction extends Action
{

    /**
     * @inheritDoc
     * @return Response
     * @throws ConflictException
     * @throws JsonException
     * @throws NoResultException
     * @throws UniqueFailedException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     */
    protected function action(): Response
    {
        $body = $this->request->getParsedBody();
        $id = $this->args['id'];
        $segmentPage = SegmentPage::findById($id);
        if ($segmentPage === null) {
            throw new NoResultException($this->request, 'Segment page not found');
        }

        if (isset($body['name'])) {
            $segmentPage->name = $body['name'];
        }

        try {
            $segmentPage->update();
        } catch (UniqueFailedException) {
            throw new ConflictException($this->request, 'Name already used');
        }

        return $this->noContent();
    }
}