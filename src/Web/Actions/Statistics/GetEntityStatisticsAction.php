<?php

namespace App\Web\Actions\Statistics;

use App\Statistics\Entity;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/statistics/entity', JinyaAction::GET)]
#[Authenticated(Authenticated::WRITER)]
class GetEntityStatisticsAction extends Action
{

    /**
     * @inheritDoc
     */
    protected function action(): Response
    {
        $entityStats = new Entity();
        return $this->respond($entityStats->getEntityShare());
    }
}