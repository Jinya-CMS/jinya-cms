<?php

namespace App\Web\Actions\Version;

use App\Web\Actions\Update\UpdateAction;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Action to get version info
 */
class GetVersionInfo extends UpdateAction
{
    /**
     * Gets the most recent version and the current version
     *
     * @throws JsonException
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
