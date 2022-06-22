<?php

namespace App\Web\Actions\Update;

use JsonException;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

/**
 * Action to get the update UI
 */
class GetUpdateAction extends UpdateAction
{
    /**
     * Renders the update UI
     *
     * @return Response
     * @throws JsonException
     * @throws Throwable
     */
    protected function action(): Response
    {
        return $this->render(
            'update::update',
            [
                'newVersion' => array_key_last($this->getReleases()),
                'version' => INSTALLED_VERSION,
            ],
        );
    }
}
