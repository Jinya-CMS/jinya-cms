<?php

namespace App\Web\Actions\Statistics;

use App\Statistics\History;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Nyholm\Psr7\Response as NyholmResponse;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/statistics/history/{type}', JinyaAction::GET)]
#[Authenticated(Authenticated::WRITER)]
class GetHistoryStatisticsAction extends Action
{

    /**
     * @inheritDoc
     * @throws InvalidQueryException
     * @throws InvalidQueryException
     */
    protected function action(): Response
    {
        $historyStats = new History();
        $type = str_replace('-', '_', $this->args['type']);
        if (!in_array($type, ['file', 'form', 'gallery', 'page', 'segment_page'])) {
            return (new NyholmResponse())
                ->withStatus(Action::HTTP_MOVED_PERMANENTLY)
                ->withHeader('Location', '/');
        }

        return $this->respond(
            [
                'created' => $this->formatIterator($historyStats->getCreatedHistory($type)),
                'updated' => $this->formatIterator($historyStats->getUpdatedHistory($type)),
            ],
        );
    }
}