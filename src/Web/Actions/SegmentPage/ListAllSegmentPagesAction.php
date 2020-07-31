<?php

namespace App\Web\Actions\SegmentPage;

use App\Database\SegmentPage;
use App\Web\Actions\Action;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class ListAllSegmentPagesAction extends Action
{

    /**
     * @inheritDoc
     * @throws JsonException
     */
    protected function action(): Response
    {
        $params = $this->request->getQueryParams();
        if (isset($params['keyword'])) {
            return $this->respondList($this->formatIterator(SegmentPage::findByKeyword($params['keyword'])));
        }

        return $this->respondList($this->formatIterator(SegmentPage::findAll()));
    }
}