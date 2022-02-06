<?php

namespace App\Web\Actions\Theme;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Theme;
use App\Theming;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\NoResultException;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;
use ScssPhp\ScssPhp\Exception\SassException;

#[JinyaAction('/api/theme/{id}/styling', JinyaAction::PUT)]
#[Authenticated(Authenticated::WRITER)]
class PutStyleVariablesAction extends ThemeAction
{
    /**
     * {@inheritDoc}
     * @throws JsonException
     * @throws NoResultException
     * @throws UniqueFailedException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws SassException
     * @throws \Jinya\PDOx\Exceptions\NoResultException
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
