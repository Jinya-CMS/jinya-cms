<?php

namespace App\Web\Actions\Statistics;

use App\Statistics\Storage;
use App\Web\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Action to get the system storage stats
 */
class GetSystemStatisticsAction extends Action
{

    /**
     * Gets the system storage statistics, currently used, free and total disk space
     *
     * @inheritDoc
     */
    protected function action(): Response
    {
        return $this->respond(
            [
                'used' => Storage::getUsedStorage(),
                'free' => Storage::getFreeStorage(),
                'total' => Storage::getTotalStorage(),
            ]
        );
    }
}