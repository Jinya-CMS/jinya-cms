<?php

namespace App\Web\Actions\Statistics;

use App\Statistics\Entity;
use App\Web\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;

/**
 *
 */
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