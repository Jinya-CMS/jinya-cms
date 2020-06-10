<?php

namespace App\Web\Actions\Theme;

use App\Database\Exceptions\UniqueFailedException;
use App\Database\Theme;
use App\Web\Actions\Action;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class PutStyleVariablesAction extends Action
{

    /**
     * @inheritDoc
     * @throws JsonException
     * @throws NoResultException
     * @throws UniqueFailedException
     */
    protected function action(): Response
    {
        $themeId = $this->args['id'];
        $theme = Theme::findById($themeId);
        if (!$theme) {
            throw new NoResultException($this->request, 'Theme not found');
        }

        $body = $this->request->getParsedBody();
        $variables = $body['variables'];

        $theme->scssVariables = $variables;
        $theme->update();

        return $this->noContent();
    }
}