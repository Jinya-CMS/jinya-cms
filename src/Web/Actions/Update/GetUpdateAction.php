<?php

namespace App\Web\Actions\Update;

use JsonException;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

/**
 *
 */
class GetUpdateAction extends UpdateAction
{
    /**
     * {@inheritDoc}
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
