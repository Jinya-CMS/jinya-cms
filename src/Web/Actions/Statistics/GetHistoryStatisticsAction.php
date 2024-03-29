<?php

namespace App\Web\Actions\Statistics;

use App\Statistics\History;
use App\Web\Actions\Action;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Nyholm\Psr7\Response as NyholmResponse;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Action to get history statistics
 * @codeCoverageIgnore
 */
class GetHistoryStatisticsAction extends Action
{

    /**
     * Gets the created and updated statistics for the given type
     *
     * @throws InvalidQueryException
     * @throws InvalidQueryException
     */
    protected function action(): Response
    {
        $type = str_replace('-', '_', $this->args['type']);
        if (!in_array($type, ['file', 'form', 'gallery', 'page', 'segment_page'])) {
            return (new NyholmResponse())
                ->withStatus(Action::HTTP_MOVED_PERMANENTLY)
                ->withHeader('Location', '/');
        }

        return $this->respond(
            [
                'created' => $this->formatIterator(History::getCreatedHistory($type)),
                'updated' => $this->formatIterator(History::getUpdatedHistory($type)),
            ],
        );
    }
}