<?php

namespace App\Web\Actions\Statistics;

use App\Statistics\Storage;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/statistics/system', JinyaAction::GET)]
#[Authenticated(Authenticated::WRITER)]
class GetSystemStatisticsAction extends Action
{

    /**
     * @inheritDoc
     */
    protected function action(): Response
    {
        $storageStats = new Storage();
        return $this->respond(
            [
                'used' => $storageStats->getUsedStorage(),
                'free' => $storageStats->getFreeStorage(),
                'total' => $storageStats->getTotalStorage(),
            ]
        );
    }
}