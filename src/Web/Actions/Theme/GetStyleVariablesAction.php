<?php

namespace App\Web\Actions\Theme;

use App\Database;
use App\Theming;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class GetStyleVariablesAction extends ThemeAction
{

    /**
     * @inheritDoc
     * @return Response
     * @throws JsonException
     * @throws NoResultException
     */
    protected function action(): Response
    {
        $themeId = $this->args['id'];
        $dbTheme = Database\Theme::findById($themeId);
        if (!$dbTheme) {
            throw new NoResultException($this->request, 'Theme not found');
        }

        $theme = new Theming\Theme($dbTheme);
        $vars = $theme->getStyleVariables();
        $dbVars = $dbTheme->scssVariables;

        return $this->respond(array_merge($vars, $dbVars));
    }
}