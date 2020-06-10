<?php

namespace App\Web\Actions\Theme;

use App\Database\Theme;
use App\Web\Actions\Action;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class PutConfigurationAction extends Action
{

    /**
     * @inheritDoc
     * @throws JsonException
     * @throws NoResultException
     */
    protected function action(): Response
    {
        $themeId = $this->args['id'];
        $theme = Theme::findById($themeId);
        if (!$theme) {
            throw new NoResultException($this->request, 'Theme not found');
        }

        $body = $this->request->getParsedBody();
        $config = $body['configuration'];

        $theme->configuration = $config;

        return $this->noContent();
    }
}