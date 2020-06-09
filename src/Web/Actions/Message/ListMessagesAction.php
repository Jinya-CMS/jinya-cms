<?php

namespace App\Web\Actions\Message;

use App\Database\Message;
use App\Web\Actions\Action;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class ListMessagesAction extends Action
{

    /**
     * @inheritDoc
     * @throws JsonException
     */
    protected function action(): Response
    {
        $queryParams = $this->request->getQueryParams();
        $keyword = $queryParams['keyword'] ?? '';
        $offset = $queryParams['offset'] ?? 0;
        $count = $queryParams['count'] ?? 10;
        if (isset($this->args['formId'])) {
            $result = Message::findByFormAndKeyword($this->args['formId'], $keyword, $offset, $count);

            return $this->respondList($this->formatIterator($result->items), $offset, $result->totalCount);
        }

        if (isset($this->args['inbox']) && in_array(strtolower($this->args['inbox']), ['spam', 'trash', 'archive'])) {
            $result = Message::findByInboxAndKeyword($this->args['inbox'], $keyword, $offset, $count);

            return $this->respondList($this->formatIterator($result->items), $offset, $result->totalCount);
        }

        $result = Message::findByFormAndKeyword(null, $keyword, $offset, $count);
        return $this->respondList($this->formatIterator($result->items), $offset, $result->totalCount);
    }
}