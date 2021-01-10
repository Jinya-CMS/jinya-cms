<?php

namespace App\Web\Actions\Version;

use App\Web\Actions\Update\UpdateAction;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/version', JinyaAction::GET)]
#[Authenticated(Authenticated::ADMIN)]
class GetVersionInfo extends UpdateAction
{

    /**
     * @inheritDoc
     */
    protected function action(): Response
    {
        return $this->respond(
            [
                'currentVersion' => INSTALLED_VERSION,
                'mostRecentVersion' => array_key_last($this->getReleases()),
            ]
        );
    }
}