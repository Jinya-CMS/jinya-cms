<?php

namespace App\Web\Actions\Theme;

use App\Database\Exceptions\UniqueFailedException;
use App\Database\Theme;
use App\Theming;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class PutStyleVariablesAction extends ThemeAction
{

    /**
     * @inheritDoc
     * @throws JsonException
     * @throws NoResultException
     * @throws UniqueFailedException
     */
    protected function action(): Response
    {
        $this->syncThemes();
        $themeId = $this->args['id'];
        $dbTheme = Theme::findById($themeId);
        if (!$dbTheme) {
            throw new NoResultException($this->request, 'Theme not found');
        }

        $body = $this->request->getParsedBody();
        $variables = $body['variables'];

        $dbTheme->scssVariables = $variables;
        $dbTheme->update();

        $theme = new Theming\Theme($dbTheme);
        $theme->compileStyleCache();

        return $this->noContent();
    }
}