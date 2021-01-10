<?php

namespace App\Web\Actions\SegmentPage;

use App\Database\Exceptions\UniqueFailedException;
use App\Database\SegmentPage;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Attributes\RequiredFields;
use App\Web\Exceptions\ConflictException;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/segment-page', JinyaAction::POST)]
#[Authenticated(Authenticated::WRITER)]
#[RequiredFields(['name'])]
class CreateSegmentPageAction extends Action
{
    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function action(): Response
    {
        $body = $this->request->getParsedBody();
        $segmentPage = new SegmentPage();
        $segmentPage->name = $body['name'];

        try {
            $segmentPage->create();
        } catch (UniqueFailedException) {
            throw new ConflictException($this->request, 'Name already used');
        }

        return $this->respond($segmentPage->format(), Action::HTTP_CREATED);
    }
}