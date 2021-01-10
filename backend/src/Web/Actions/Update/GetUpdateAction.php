<?php

namespace App\Web\Actions\Update;

use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class GetUpdateAction extends UpdateAction
{

    /**
     * @inheritDoc
     * @throws JsonException
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