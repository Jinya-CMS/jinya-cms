<?php

namespace App\Web\Actions\Statistics;

use App\Statistics\Entity;
use App\Web\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Action to get entity share in Jinya CMS
 */
class GetEntityStatisticsAction extends Action
{

    /**
     * Gets the entity share
     *
     * @return Response
     */
    protected function action(): Response
    {
        return $this->respond(Entity::getEntityShare());
    }
}